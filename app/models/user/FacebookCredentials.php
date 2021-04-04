<?php

declare(strict_types=1);

namespace PP\User;

use Nette\SmartObject;
use Nette\Utils\Validators;

/**
 * @author Andrej Souček
 */
class FacebookCredentials implements Credentials
{
    use SmartObject;

    private string $email;

    private string $uid;

    private string $firstName;

    public function __construct(string $email, string $uid, string $firstName)
    {
        Validators::assert($email, 'email');
        Validators::assert($uid, 'string:1..');
        Validators::assert($firstName, 'string:1..');
        $this->email = strtolower($email);
        $this->uid = $uid;
        $this->firstName = $firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAuthString(): string
    {
        return $this->uid;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }
}
