<?php

declare(strict_types=1);

namespace PP\API;

use Nette\Application\AbortException;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;
use PP\User\TokenCredentials;
use RuntimeException;

/**
 * @author Andrej Souček
 */
class LoginPresenter extends Presenter
{

    /**
     * @throws AbortException
     */
    public function actionCreate(): void
    {
        try {
            $req = $this->getRequest();
            if ($req === null) {
                throw new RuntimeException();
            }
            $email = $req->getPost('email');
            $key = $req->getPost('key');
            $this->getUser()->login(new TokenCredentials($email, $key));
        } catch (AuthenticationException $e) {
            $this->getHttpResponse()->setCode(401);
            $this->sendResponse(new JsonResponse(['error' => 'Incorrect e-mail or key.']));
        }
        if ($this->getSession()->isStarted()) {
            $this->getSession()->close();
            $this->getSession()->destroy();
        }
        $this->getSession()->setExpiration('4 hours');
        $this->getHttpResponse()->setCode(200);
        $this->sendResponse(new JsonResponse(['session' => $this->getSession()->getId()]));
    }

    public function sendTemplate(): void
    {
    }
}
