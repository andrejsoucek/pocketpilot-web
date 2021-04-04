<?php

declare(strict_types=1);

namespace PP\Dashboard;

use Nette\Database\Context;
use Nette\Database\IRow;
use Nette\SmartObject;
use PDOException;
use RuntimeException;

/**
 * @author Andrej Souček
 */
class DashboardRead
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @throws RuntimeException
     */
    public function fetchAll(): array
    {
        try {
            $rows = $this->database->table(DashboardDatabaseDef::TABLE_NAME)->order('id DESC')->fetchAll();
            $ret = [];
            foreach ($rows as $row) {
                $ret[$row[DashboardDatabaseDef::COLUMN_ID]] = $this->toEntity($row);
            }
            return $ret;
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    private function toEntity(IRow $data): DashboardEntry
    {
        return new DashboardEntry(
            $data->offsetGet(DashboardDatabaseDef::COLUMN_ID),
            $data->offsetGet(DashboardDatabaseDef::COLUMN_TITLE),
            $data->offsetGet(DashboardDatabaseDef::COLUMN_DATE),
            $data->offsetGet(DashboardDatabaseDef::COLUMN_ITEM_1),
            $data->offsetGet(DashboardDatabaseDef::COLUMN_ITEM_2),
            $data->offsetGet(DashboardDatabaseDef::COLUMN_ITEM_3),
            $data->offsetGet(DashboardDatabaseDef::COLUMN_ITEM_4)
        );
    }
}
