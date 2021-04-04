<?php

declare(strict_types=1);

namespace PP\POI;

use Nette\Database\Context;
use Nette\Database\IRow;
use Nette\SmartObject;

/**
 * @author Andrej Souček
 */
class POIRead
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @return array<POIEntry>
     */
    public function fetchAll(): array
    {
        $ret = [];
        $rows = $this->database
            ->table(POIDatabaseDef::TABLE_NAME)
            ->select(
                POIDatabaseDef::COLUMN_NAME  . ',' .
                POIDatabaseDef::COLUMN_DESCRIPTION . ',' .
                'ST_X(' . POIDatabaseDef::COLUMN_LOCATION . '::geometry) AS ' . POIDatabaseDef::ALIAS_LONGITUDE . ',' .
                'ST_Y(' . POIDatabaseDef::COLUMN_LOCATION . '::geometry) AS ' . POIDatabaseDef::ALIAS_LATITUDE
            )
            ->order(POIDatabaseDef::COLUMN_NAME);
        foreach ($rows as $row) {
            $ret[] = $this->toEntity($row);
        }
        return $ret;
    }

    public function fetchClosestPoiTo(string $lng, string $lat, int $range, ?bool $hasRunway = null): ?POIEntry
    {
        $row = $this->database
            ->query(
                "SELECT " .
                    POIDatabaseDef::COLUMN_NAME  . ',' .
                    POIDatabaseDef::COLUMN_DESCRIPTION . ',' .
                    'ST_X(' . POIDatabaseDef::COLUMN_LOCATION . '::geometry) 
                        AS ' . POIDatabaseDef::ALIAS_LONGITUDE . ',' .
                    'ST_Y(' . POIDatabaseDef::COLUMN_LOCATION . '::geometry) 
                        AS ' . POIDatabaseDef::ALIAS_LATITUDE .
                " FROM (
                    SELECT 
                        *, 
                        ST_Distance(" . POIDatabaseDef::COLUMN_LOCATION . ", ?::geography) 
                        AS " . POIDatabaseDef::ALIAS_DISTANCE .
                    " FROM " . POIDatabaseDef::TABLE_NAME . ") a 
                WHERE " . POIDatabaseDef::ALIAS_DISTANCE . " < ?"
                . self::addRunwayCondition($hasRunway) .
                " ORDER BY distance LIMIT 1",
                "SRID=4326;POINT($lng $lat)",
                $range
            )->fetch();
        return $row ? $this->toEntity($row) : null;
    }

    private static function addRunwayCondition(?bool $runway): string
    {
        if ($runway === null) {
            return '';
        }
        if ($runway === false) {
            return ' AND ' . POIDatabaseDef::COLUMN_NAME . " !~ '^(LK|LZ)[A-Z].+$'";
        }
        return ' AND ' . POIDatabaseDef::COLUMN_NAME . " ~ '^(LK|LZ)[A-Z].+$'";
    }

    private function toEntity(IRow $data): POIEntry
    {
        return new POIEntry(
            (string)$data->offsetGet(POIDatabaseDef::COLUMN_NAME),
            (string)$data->offsetGet(POIDatabaseDef::COLUMN_DESCRIPTION),
            (string)$data->offsetGet(POIDatabaseDef::ALIAS_LONGITUDE),
            (string)$data->offsetGet(POIDatabaseDef::ALIAS_LATITUDE)
        );
    }
}
