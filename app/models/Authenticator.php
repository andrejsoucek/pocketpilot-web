<?php

declare(strict_types=1);

namespace PP;

use InvalidArgumentException;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;
use Nette\Security\Identity;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use PP\User\Credentials;
use PP\User\PasswordCredentials;
use PP\User\PasswordAuthenticator;
use PP\User\TokenAuthenticator;
use PP\User\TokenCredentials;
use PP\User\UserEntry;
use UnexpectedValueException;

/**
 * @author Andrej Souček
 */
class Authenticator implements IAuthenticator
{
    use SmartObject;

    private PasswordAuthenticator $pwAuthenticator;

    private TokenAuthenticator $tokenAuthenticator;

    public function __construct(
        PasswordAuthenticator $pwAuthenticator,
        TokenAuthenticator $tokenAuthenticator
    ) {
        $this->pwAuthenticator = $pwAuthenticator;
        $this->tokenAuthenticator = $tokenAuthenticator;
    }

    /**
     * @param Credentials[] $credentialsArr
     * @throws AuthenticationException
     * @throws AssertionException
     */
    public function authenticate(array $credentialsArr): IIdentity
    {
        if (count($credentialsArr)) {
            $credentials = $credentialsArr[0];
        } else {
            throw new InvalidArgumentException('$credentials array must contain exactly one value.');
        }
        try {
            switch (true) {
                case $credentials instanceof PasswordCredentials:
                    $user = $this->pwAuthenticator->authenticate($credentials);
                    break;
                case $credentials instanceof TokenCredentials:
                    $user = $this->tokenAuthenticator->authenticate($credentials);
                    break;
                default:
                    throw new UnexpectedValueException(
                        'Only PasswordCredentials or TokenCredentials allowed.'
                    );
            }
            return $this->createIdentity($user);
        } catch (IncorrectCredentialsException $e) {
            throw new AuthenticationException($e->getMessage(), self::FAILURE, $e);
        }
    }

    private function createIdentity(UserEntry $user): Identity
    {
        return new Identity(
            $user->getId(),
            [$user->getRole()],
            ["name" => $user->getName(), "username" => $user->getName(), "email" => $user->getEmail()]
        );
    }
}
