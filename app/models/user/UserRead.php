<?php

declare(strict_types=1);

namespace PP\User;

use Nette\Database\Context;
use Nette\Database\IRow;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;
use PP\IncorrectCredentialsException;

/**
 * @author Andrej Souček
 */
class UserRead
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    /**
     * @throws IncorrectCredentialsException
     * @throws AssertionException
     */
    public function fetchBy(string $email): UserEntry
    {
        Validators::assert($email, 'email');
        $row = $this->database->table(UserDatabaseDef::TABLE_NAME)
            ->where(UserDatabaseDef::COLUMN_EMAIL, $email)->fetch();
        if ($row) {
            return $this->toEntity($row);
        }

        throw new IncorrectCredentialsException("User with email '$email' does not exists.");
    }

    /**
     * @param IRow $data
     * @return UserEntry
     */
    private function toEntity(IRow $data): UserEntry
    {
        return new UserEntry(
            $data->offsetGet(UserDatabaseDef::COLUMN_ID),
            $data->offsetGet(UserDatabaseDef::COLUMN_NAME),
            $data->offsetGet(UserDatabaseDef::COLUMN_EMAIL),
            $data->offsetGet(UserDatabaseDef::COLUMN_ROLE),
            $data->offsetGet(UserDatabaseDef::COLUMN_TOKEN)
        );
    }
}
