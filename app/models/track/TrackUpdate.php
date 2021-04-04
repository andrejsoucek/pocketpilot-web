<?php

declare(strict_types=1);

namespace PP\Track;

use Nette\Database\Context;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;
use PDOException;
use RuntimeException;

/**
 * @author Andrej Souček
 */
class TrackUpdate
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @param int $trackId
     * @param string $trackName
     * @param array $waypoints
     * @return int
     * @throws AssertionException
     * @throws RuntimeException
     */
    public function process(int $trackId, string $trackName, array $waypoints): int
    {
        Validators::assert($trackId, 'numericint:1..');
        Validators::assert($trackName, 'string:1..');
        try {
            return $this->database->table(TrackDatabaseDef::TABLE_NAME)
                ->where(TrackDatabaseDef::COLUMN_ID, $trackId)
                ->update([
                    TrackDatabaseDef::COLUMN_NAME => $trackName,
                    TrackDatabaseDef::COLUMN_TRACK => $this->database::literal($this->prepareQuery($waypoints))
                ]);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    private function prepareQuery(array $waypoints): string
    {
        $out = 'ST_GeomFromText(\'LINESTRING(';
        $points = [];
        foreach ($waypoints as $waypoint) {
            $lng = $waypoint['lng'];
            $lat = $waypoint['lat'];
            $points[] = "$lng $lat";
        }
        $out .= implode(',', $points);
        $out .= ')\')';
        return $out;
    }
}
