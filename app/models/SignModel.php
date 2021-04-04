<?php

declare(strict_types=1);

namespace PP;

use Nette\Security\AuthenticationException;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use PP\User\FacebookCredentials;
use PP\User\UserRegister;

/**
 * @author Andrej Souček
 */
class SignModel
{
    use SmartObject;

    private UserRegister $register;

    private FacebookService $fb;

    public function __construct(UserRegister $register, FacebookService $fb)
    {
        $this->register = $register;
        $this->fb = $fb;
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

    /**
     * @return FacebookCredentials
     * @throws AuthenticationException
     */
    public function getFacebookCredentials(): FacebookCredentials
    {
        $graphUser = $this->fb->fetchUser();
        if (
            $graphUser->getEmail() !== null &&
            $graphUser->getId() !== null &&
            $graphUser->getFirstName() !== null
        ) {
            return new FacebookCredentials($graphUser->getEmail(), $graphUser->getId(), $graphUser->getFirstName());
        }

        throw new AuthenticationException('Missing information in Facebook response.');
    }

    /**
     * @throws AssertionException
     */
    public function generateLoginUrl(string $redirectUrl): string
    {
        return $this->fb->generateLoginUrl($redirectUrl);
    }
}
