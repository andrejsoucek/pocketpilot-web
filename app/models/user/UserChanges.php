<?php

declare(strict_types=1);

namespace PP\User;

use Nette\SmartObject;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;

/**
 * @author Andrej Souček
 */
class UserChanges
{
    use SmartObject;

    private int $id;

    private string $name;

    private string $email;

    private string $role;

    /**
     * @throws AssertionException
     */
    public function __construct(int $id)
    {
        Validators::assert($id, 'int..');
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @throws AssertionException
     */
    public function setName(string $name): void
    {
        Validators::assert($name, 'string:1..');
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @throws AssertionException
     */
    public function setEmail(string $email): void
    {
        Validators::assert($email, 'email');
        $this->email = $email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @throws AssertionException
     */
    public function setRole(string $role): void
    {
        Validators::assert($role, 'string:1..');
        $this->role = $role;
    }
}
