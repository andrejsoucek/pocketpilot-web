<?php

declare(strict_types=1);

namespace PP\User;

use Nette\Database\Context;
use Nette\Security\IIdentity;
use Nette\SmartObject;
use Nette\Utils\DateTime;

/**
 * @author Andrej Souček
 */
class UserUpdate
{
    use SmartObject;

    private Context $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function process(UserChanges $changes): void
    {
        $this->database->table(UserDatabaseDef::TABLE_NAME)
            ->where(UserDatabaseDef::COLUMN_ID, $changes->getId())
            ->update($this->toArray($changes));
    }

    public function regenerateTokenFor(IIdentity $user): string
    {
        $newToken = md5($user->username . new DateTime());
        $this->database->table(UserDatabaseDef::TABLE_NAME)
            ->where(UserDatabaseDef::COLUMN_ID, $user->getId())
            ->update([UserDatabaseDef::COLUMN_TOKEN => md5($user->username . new DateTime())]);

        return $newToken;
    }

    /**
     * @param UserChanges $changes
     * @return array
     */
    private function toArray(UserChanges $changes): array
    {
        $xs = [
            UserDatabaseDef::COLUMN_NAME => $changes->getName(),
            UserDatabaseDef::COLUMN_EMAIL => $changes->getEmail(),
            UserDatabaseDef::COLUMN_ROLE => $changes->getRole()
        ];

        return array_filter($xs);
    }
}
