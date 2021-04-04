<?php

declare(strict_types=1);

namespace PP\User;

use Nette\Database\Context;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use PP\IncorrectCredentialsException;

/**
 * @author Andrej Souček
 */
class FacebookAuthenticator
{
    use SmartObject;

    private Context $database;

    private UserRead $read;

    private UserRegister $register;

    public function __construct(Context $database, UserRead $read, UserRegister $register)
    {
        $this->database = $database;
        $this->read = $read;
        $this->register = $register;
    }

    /**
     * @param FacebookCredentials $credentials
     * @return UserEntry
     * @throws AssertionException
     * @throws IncorrectCredentialsException
     */
    public function authenticate(FacebookCredentials $credentials): UserEntry
    {
        $row = $this->database->table(UserDatabaseDef::TABLE_NAME)
            ->where(UserDatabaseDef::COLUMN_EMAIL, $credentials->getEmail())->fetch();
        if (!$row) {
            $row = $this->register->process(
                $credentials->getFirstName(),
                $credentials->getEmail(),
                $credentials->getAuthString()
            );
        }
        if ($row !== null && !isset($row[UserDatabaseDef::COLUMN_FB_UID])) {
            $this->updateMissingUid($credentials);
        }
        if (
            $row !== null &&
            isset($row[UserDatabaseDef::COLUMN_FB_UID]) &&
            $row[UserDatabaseDef::COLUMN_FB_UID] === $credentials->getAuthString()
        ) {
            return $this->read->fetchBy($credentials->getEmail());
        }

        throw new IncorrectCredentialsException("FB user ID does not match with local FB uID.");
    }

    /**
     * @param FacebookCredentials $credentials
     */
    private function updateMissingUid(FacebookCredentials $credentials): void
    {
        $this->database->table(UserDatabaseDef::TABLE_NAME)
            ->where(UserDatabaseDef::COLUMN_EMAIL, $credentials->getEmail())
            ->update([UserDatabaseDef::COLUMN_FB_UID => $credentials->getAuthString()]);
    }
}
