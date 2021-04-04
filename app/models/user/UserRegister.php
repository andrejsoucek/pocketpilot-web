<?php

declare(strict_types=1);

namespace PP\User;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Security\Passwords;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;
use PP\IncorrectCredentialsException;

/**
 * @author Andrej Souček
 */
class UserRegister
{
    use SmartObject;

    private Context $database;

    private Passwords $passwords;

    public function __construct(Context $database, Passwords $passwords)
    {
        $this->database = $database;
        $this->passwords = $passwords;
    }

    /**
     * @throws IncorrectCredentialsException
     * @throws AssertionException
     */
    public function process(
        string $username,
        string $email,
        ?string $fbUid = null,
        ?string $password = null
    ): ActiveRow {
        Validators::assert($username, 'string:1..');
        Validators::assert($email, 'email');
        Validators::assert($password, 'string:1..|null');
        Validators::assert($fbUid, 'string:1..|null');
        try {
            return $this->database->table(UserDatabaseDef::TABLE_NAME)->insert(array(
                UserDatabaseDef::COLUMN_NAME => $username,
                UserDatabaseDef::COLUMN_EMAIL => strtolower($email),
                UserDatabaseDef::COLUMN_FB_UID => $fbUid,
                UserDatabaseDef::COLUMN_PASSWORD_HASH => $password ? $this->passwords->hash($password) : null,
            ));
        } catch (UniqueConstraintViolationException $e) {
            throw new IncorrectCredentialsException("Username already exists.");
        }
    }
}
