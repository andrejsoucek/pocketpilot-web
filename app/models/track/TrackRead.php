<?php

declare(strict_types=1);

namespace PP\Track;

use Nette\Database\Context;
use Nette\Database\IRow;
use Nette\SmartObject;
use PDOException;
use RuntimeException;

/**
 * @author Andrej Souček
 */
class TrackRead
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @return array<int, TrackEntry>
     * @throws RuntimeException
     */
    public function fetchForUser(int $userId): array
    {
        try {
            $ret = [];
            $rows = $this->database->table(TrackDatabaseDef::TABLE_NAME)
                ->select(
                    TrackDatabaseDef::COLUMN_ID . ',' .
                    TrackDatabaseDef::COLUMN_USER_ID . ',' .
                    TrackDatabaseDef::COLUMN_NAME . ',' .
                    TrackDatabaseDef::COLUMN_CREATION_DATE . ',' .
                    'ST_Length(' . TrackDatabaseDef::COLUMN_TRACK . ') AS length' . ',' .
                    'ST_AsGeoJSON(' . TrackDatabaseDef::COLUMN_TRACK . ') AS geojson' . ',' .
                    TrackDatabaseDef::COLUMN_HASH
                )
                ->where(TrackDatabaseDef::COLUMN_USER_ID, $userId)->fetchAll();
            foreach ($rows as $row) {
                $ret[(int) $row[TrackDatabaseDef::COLUMN_ID]] = $this->toEntity($row);
            }
            return $ret;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function fetchByHash(string $hash): ?TrackEntry
    {
        try {
            $row = $this->database->table(TrackDatabaseDef::TABLE_NAME)
                ->select(
                    TrackDatabaseDef::COLUMN_ID . ',' .
                    TrackDatabaseDef::COLUMN_USER_ID . ',' .
                    TrackDatabaseDef::COLUMN_NAME . ',' .
                    TrackDatabaseDef::COLUMN_CREATION_DATE . ',' .
                    'ST_Length(' . TrackDatabaseDef::COLUMN_TRACK . ') AS length' . ',' .
                    'ST_AsGeoJSON(' . TrackDatabaseDef::COLUMN_TRACK . ') AS geojson' . ',' .
                    TrackDatabaseDef::COLUMN_HASH
                )
                ->where(TrackDatabaseDef::COLUMN_HASH, $hash)->fetch();
            return $row ? $this->toEntity($row) : null;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    private function toEntity(IRow $data): TrackEntry
    {
        return new TrackEntry(
            $data->offsetGet(TrackDatabaseDef::COLUMN_ID),
            $data->offsetGet('geojson'),
            (float)$data->offsetGet('length'),
            $data->offsetGet(TrackDatabaseDef::COLUMN_USER_ID),
            $data->offsetGet(TrackDatabaseDef::COLUMN_NAME),
            $data->offsetGet(TrackDatabaseDef::COLUMN_CREATION_DATE),
            $data->offsetGet(TrackDatabaseDef::COLUMN_HASH)
        );
    }
}
