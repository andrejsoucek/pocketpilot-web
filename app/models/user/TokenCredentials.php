<?php

declare(strict_types=1);

namespace PP\User;

use Nette\SmartObject;
use Nette\Utils\Validators;

/**
 * @author Andrej Souček
 */
class TokenCredentials implements Credentials
{
    use SmartObject;

    private string $email;

    private string $token;

    public function __construct(string $email, string $token)
    {
        Validators::assert($email, 'email');
        Validators::assert($token, 'string:32');
        $this->email = strtolower($email);
        $this->token = $token;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAuthString(): string
    {
        return $this->token;
    }
}
