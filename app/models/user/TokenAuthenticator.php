<?php

declare(strict_types=1);

namespace PP\User;

use Nette\Database\Context;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use PP\IncorrectCredentialsException;

class TokenAuthenticator
{
    use SmartObject;

    private Context $database;

    private UserRead $read;

    public function __construct(Context $database, UserRead $read)
    {
        $this->database = $database;
        $this->read = $read;
    }

    /**
     * @throws IncorrectCredentialsException
     * @throws AssertionException
     */
    public function authenticate(TokenCredentials $credentials): UserEntry
    {
        $entry = $this->read->fetchBy($credentials->getEmail());
        $row = $this->database->table(UserDatabaseDef::TABLE_NAME)
            ->where(UserDatabaseDef::COLUMN_ID, $entry->getId())
            ->fetch();
        if ($row === null || $credentials->getAuthString() !== $row->token) {
            throw new IncorrectCredentialsException('Token is incorrect.');
        }

        return $entry;
    }
}
