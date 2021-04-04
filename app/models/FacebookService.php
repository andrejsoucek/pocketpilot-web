<?php

declare(strict_types=1);

namespace PP;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;
use Nette\Security\AuthenticationException;
use Nette\SmartObject;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;

/**
 * @author Andrej Souček
 */
class FacebookService
{
    use SmartObject;

    private Facebook $fb;

    public function __construct(Facebook $fb)
    {
        $this->fb = $fb;
    }

    /**
     * @throws AssertionException
     */
    public function generateLoginUrl(string $redirectUrl): string
    {
        Validators::assert($redirectUrl, 'url');
        return $this->fb->getRedirectLoginHelper()->getLoginUrl($redirectUrl, ['email']);
    }


    /**
     * @return GraphUser<object>
     * @throws AuthenticationException
     */
    public function fetchUser(): GraphUser
    {
        try {
            $response = $this->fb->get(
                '/me?fields=email,first_name,id',
                $this->fb->getRedirectLoginHelper()->getAccessToken()
            );
            return $response->getGraphUser();
        } catch (FacebookSDKException $e) {
            throw new AuthenticationException("Error while FB authentication.");
        }
    }
}
