<?php

declare(strict_types=1);

namespace PP\User;

use Nette\SmartObject;
use Nette\Utils\Validators;

class UserEntry
{
    use SmartObject;

    private int $id;

    private string $name;

    private string $email;

    private string $role;

    private string $token;

    public function __construct(int $id, string $name, string $email, string $role, string $token)
    {
        Validators::assert($id, 'int:1..');
        Validators::assert($name, 'string:1..');
        Validators::assert($email, 'email');
        Validators::assert($role, 'string:1..');
        Validators::assert($token, 'string:32');
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->token = $token;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
