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
class TrackDelete
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @param int $trackId
     * @param int $userId
     * @return int
     * @throws AssertionException
     */
    public function process(int $trackId, int $userId): int
    {
        try {
            Validators::assert($trackId, 'numericint:1..');
            return $this->database->table(TrackDatabaseDef::TABLE_NAME)
                ->where(TrackDatabaseDef::COLUMN_ID, $trackId)
                ->where(TrackDatabaseDef::COLUMN_USER_ID, $userId)
                ->delete();
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}
