<?php

declare(strict_types=1);

namespace PP;

use Nette\SmartObject;
use Nette\Utils\AssertionException;
use PP\User\UserRegister;

/**
 * @author Andrej Souček
 */
class SignModel
{
    use SmartObject;

    private UserRegister $register;

    public function __construct(UserRegister $register)
    {
        $this->register = $register;
    }

    /**
     * @throws IncorrectCredentialsException
     * @throws AssertionException
     */
    public function registerUser(
        string $username,
        string $email,
        ?string $fb_uid = null,
        ?string $password = null
    ): void {
        $this->register->process($username, $email, $fb_uid, $password);
    }
}
