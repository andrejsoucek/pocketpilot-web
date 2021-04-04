<?php

declare(strict_types=1);

namespace PP\Track;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;
use PDOException;
use RuntimeException;

/**
 * @author Andrej Souček
 */
class TrackCreate
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @throws AssertionException
     * @throws RuntimeException
     */
    public function process(string $trackName, int $userId, array $waypoints): ActiveRow
    {
        Validators::assert($trackName, 'string:1..');
        Validators::assert($userId, 'numericint:1..');
        try {
            return $this->database->table(TrackDatabaseDef::TABLE_NAME)->insert([
                TrackDatabaseDef::COLUMN_NAME => $trackName,
                TrackDatabaseDef::COLUMN_USER_ID => $userId,
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
