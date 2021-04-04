<?php

declare(strict_types=1);

namespace PP\User;

use Nette\SmartObject;
use Nette\Utils\Validators;

/**
 * @author Andrej Souček
 */
class PasswordCredentials implements Credentials
{
    use SmartObject;

    private string $email;

    private string $password;

    public function __construct(string $email, string $password)
    {
        Validators::assert($email, 'email');
        Validators::assert($password, 'string:1..');
        $this->email = strtolower($email);
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAuthString(): string
    {
        return $this->password;
    }
}
