<?php

declare(strict_types=1);

namespace PP\Terrain;

use Nette\Database\Context;
use Nette\Database\IRow;
use Nette\Database\Row;
use Nette\SmartObject;

/**
 * @author Andrej Souček
 */
class TerrainRead
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @param array<int, array<int, string>> $latlngs
     * @return array<TerrainEntry>
     */
    public function fetchTerrain(array $latlngs): array
    {
        $xs = [];
        $linestring = self::getLinestringBy($latlngs);
        $points = $this->database->query(
            "SELECT " .
            "(point).geom " .
            "FROM (SELECT ST_DumpPoints(ST_Segmentize(?::geography, 500)::geometry) AS point) AS points",
            $linestring
        )->fetchAll();
        if (count($points) === 2 && $points[0]['geom'] === $points[1]['geom']) {
            $row = $this->fetch($points[0]['geom'], $linestring);
            if ($row) {
                $xs[] = new TerrainEntry(
                    0.0,
                    (float) $row[TerrainDatabaseDef::ALIAS_ELEVATION]
                );
                $xs[] = new TerrainEntry(
                    1.0,
                    (float) $row[TerrainDatabaseDef::ALIAS_ELEVATION]
                );
            }
        }
        /** @var Row $point */
        foreach ($points as $i => $point) {
            $row = $this->fetch($point['geom'], $linestring);
            if ($row) {
                $isLast = $i === count($points) - 1;
                $distance = $row[TerrainDatabaseDef::ALIAS_DISTANCE];
                if ($isLast && $distance === 0.0) {
                    $distance = 1;
                }
                $xs[] = new TerrainEntry(
                    (float) $distance,
                    (float) $row[TerrainDatabaseDef::ALIAS_ELEVATION]
                );
            }
        }
        return $xs;
    }

    private function fetch(string $geom, string $linestring): ?IRow
    {
        $x = $this->database->query(
            "SELECT " .
            "ST_LineLocatePoint(
                    ST_GeomFromText(?, 4326), 
                    ST_SetSRID('$geom'::geometry, 4326)
                ) AS distance, " .
            "ST_Value(" .
            TerrainDatabaseDef::COLUMN_RASTER . ", 
                    ST_Transform(ST_SetSRID('$geom'::geometry, 4326), 5514)
                ) AS " . TerrainDatabaseDef::ALIAS_ELEVATION . " " .
            "FROM " . TerrainDatabaseDef::TABLE_NAME . " " .
            "WHERE ST_Intersects(" .
            TerrainDatabaseDef::COLUMN_RASTER . ", 
                    ST_Transform(ST_SetSRID('$geom'::geometry, 4326), 5514)
                )",
            $linestring
        );
        return $x->fetch();
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
