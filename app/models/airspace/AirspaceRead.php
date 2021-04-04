<?php

declare(strict_types=1);

namespace PP\Airspace;

use ArrayObject;
use Nette\Database\Context;
use Nette\Database\IRow;
use Nette\SmartObject;

/**
 * @author Andrej Souček
 */
class AirspaceRead
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @param array<int, array<int, string>> $latlngs
     * @return array<AirspaceEntry>
     */
    public function fetchIntersections(array $latlngs): array
    {
        $ret = [];
        $rows = $this->database
            ->query(
                "SELECT " .
                    AirspaceDatabaseDef::COLUMN_NAME  . ', ' .
                    AirspaceDatabaseDef::COLUMN_TYPE  . ', ' .
                    AirspaceDatabaseDef::COLUMN_LOWER_BOUND  . ', ' .
                    AirspaceDatabaseDef::COLUMN_LOWER_BOUND_DATUM  . ', ' .
                    AirspaceDatabaseDef::COLUMN_UPPER_BOUND  . ', ' .
                    AirspaceDatabaseDef::COLUMN_UPPER_BOUND_DATUM  . ' ' .
                "FROM " . AirspaceDatabaseDef::TABLE_NAME . ' ' .
                "WHERE ST_Intersects(" . AirspaceDatabaseDef::COLUMN_LOCATION . ", ST_GeomFromText(?, 4326)) " .
                "AND " . AirspaceDatabaseDef::COLUMN_LOWER_BOUND_DATUM . " NOT LIKE 'FL' " .
                "AND " . AirspaceDatabaseDef::COLUMN_TYPE . " NOT IN ('SECTOR', 'FIR') " .
                "ORDER BY " . AirspaceDatabaseDef::COLUMN_TYPE . " ASC, " . AirspaceDatabaseDef::COLUMN_NAME . " ASC",
                self::getLinestringBy($latlngs)
            )->fetchAll();
        foreach ($rows as $row) {
            $horizontalIntersections = $this->fetchHorizontalIntersections(
                $row[AirspaceDatabaseDef::COLUMN_TYPE],
                $row[AirspaceDatabaseDef::COLUMN_NAME],
                $latlngs
            );
            $ret[] = $this->toEntity($row, $horizontalIntersections);
        }
        return $ret;
    }

    /**
     * @param array<int, array<int, string>> $latlngs
     */
    private function fetchHorizontalIntersections(string $type, string $name, array $latlngs): array
    {
        if (count($latlngs) === 2 && $latlngs[0] === $latlngs[1]) { // workaround for a point
            return [new ArrayObject([AirspaceDatabaseDef::ALIAS_START => 0, AirspaceDatabaseDef::ALIAS_END => 1])];
        }
        return $this->database->query(
            "SELECT 
                ST_LineLocatePoint(
                    track::geometry, ST_StartPoint((intersection).geom)
                ) as " . AirspaceDatabaseDef::ALIAS_START . ",
                ST_LineLocatePoint(
                    track::geometry, ST_EndPoint((intersection).geom)
                    ) as " . AirspaceDatabaseDef::ALIAS_END . "
            FROM
                (SELECT track, ST_Dump(intersections) as intersection 
                FROM
                    (SELECT 
                        track::geometry as track,
                        ST_Intersection(
                        track,
                        (SELECT location FROM airspace WHERE type=? AND name=?)::geometry) as intersections
                    FROM ST_GeomFromText(?, 4326) as track
                ) as a
            ) as b;",
            $type,
            $name,
            self::getLinestringBy($latlngs)
        )->fetchAll();
    }

    private function toEntity(IRow $data, array $horizontalIntersections): AirspaceEntry
    {
        return new AirspaceEntry(
            (string)$data->offsetGet(AirspaceDatabaseDef::COLUMN_NAME),
            (string)$data->offsetGet(AirspaceDatabaseDef::COLUMN_TYPE),
            new VerticalBounds(
                $data->offsetGet(AirspaceDatabaseDef::COLUMN_LOWER_BOUND),
                $data->offsetGet(AirspaceDatabaseDef::COLUMN_LOWER_BOUND_DATUM),
                $data->offsetGet(AirspaceDatabaseDef::COLUMN_UPPER_BOUND),
                $data->offsetGet(AirspaceDatabaseDef::COLUMN_UPPER_BOUND_DATUM)
            ),
            HorizontalBounds::createFrom($horizontalIntersections)
        );
    }

    private static function getLinestringBy(array $latlngs): string
    {
        $out = 'LINESTRING(';
        $out .= implode(', ', array_map(static function ($latlng) {
            return "$latlng[1] $latlng[0]";
        }, $latlngs));
        $out .= ')';
        return $out;
    }
}
