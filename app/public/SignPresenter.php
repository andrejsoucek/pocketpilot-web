<?php

declare(strict_types=1);

namespace PP\Presenters;

use GettextTranslator\Gettext;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use PP\Controls\LoginForm;
use PP\Controls\LoginFormFactory;
use PP\Controls\RegisterForm;
use PP\Controls\RegisterFormFactory;
use PP\DirResolver;
use PP\IncorrectCredentialsException;
use PP\SignModel;
use PP\User\PasswordCredentials;

/**
 * @author Andrej Souček
 */
class SignPresenter extends AppPresenter
{

    private SignModel $model;

    private LoginFormFactory $loginFormFactory;

    private RegisterFormFactory $registerFormFactory;

    private string $hCaptchaSiteKey;

    public function __construct(
        string $hCaptchaSiteKey,
        SignModel $model,
        LoginFormFactory $loginFormFactory,
        RegisterFormFactory $registerFormFactory
    ) {
        parent::__construct();
        $this->model = $model;
        $this->loginFormFactory = $loginFormFactory;
        $this->registerFormFactory = $registerFormFactory;
        $this->hCaptchaSiteKey = $hCaptchaSiteKey;
    }

    /**
     * @throws AbortException
     */
    public function actionFbLogin(): void
    {
        try {
            $this->getUser()->login($this->model->getFacebookCredentials());
        } catch (AuthenticationException $e) {
            $this->flashMessage($this->translator->translate("Error while connecting to Facebook"));
        }
        $this->redirect('Sign:');
    }

    /**
     * @throws AbortException
     */
    public function actionLogOut(): void
    {
        $this->getUser()->logout();
        $this->redirect('Sign:');
    }

    public function renderDefault(): void
    {
        $this->template->currentUserName = $this->getCurrentUserName();
        $this->template->lang = $this->getLang();
    }

    public function renderRegister(): void
    {
        $this->template->lang = $this->getLang();
    }

    public function getCurrentUserName(): string
    {
        return $this->getUser()->getIdentity() ? $this->getUser()->getIdentity()->username : "unknown";
    }

    public function getLang(): string
    {
        return $this->translator->getLang();
    }

    protected function createComponentLoginForm(): LoginForm
    {
        $form = $this->loginFormFactory->create(
            $this->model->generateLoginUrl($this->link('//fbLogin'))
        );
        $form->onSuccess[] = function (): void {
            $this->redirect('Dashboard:');
        };

        return $form;
    }

    protected function createComponentRegisterForm(): RegisterForm
    {
        $form = $this->registerFormFactory->create($this->hCaptchaSiteKey);
        $form->onSuccess[] = function (): void {
            $this->flashMessage($this->translator->translate('Sign up successful, now you can log in.'));
            $this->redirect('Homepage:');
        };

        return $form;
    }
}
