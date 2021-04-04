<?php

declare(strict_types=1);

namespace PP\Presenters;

use PP\Controls\NewPasswordForm;
use PP\Controls\NewPasswordFormFactory;
use PP\Controls\PasswordRecoveryForm;
use PP\Controls\PasswordRecoveryFormFactory;
use PP\User\PasswordReset;
use PP\User\UserEntry;
use PP\User\UserRead;

/**
 * @author Andrej Souček
 */
class PasswordRecoveryPresenter extends AppPresenter
{
    private PasswordReset $pwReset;

    private PasswordRecoveryFormFactory $passwordRecoveryFormFactory;

    private NewPasswordFormFactory $newPasswordFormFactory;

    public function __construct(
        PasswordReset $pwReset,
        PasswordRecoveryFormFactory $passwordRecoveryFormFactory,
        NewPasswordFormFactory $newPasswordFormFactory
    ) {
        parent::__construct();
        $this->pwReset = $pwReset;
        $this->passwordRecoveryFormFactory = $passwordRecoveryFormFactory;
        $this->newPasswordFormFactory = $newPasswordFormFactory;
    }

    public function actionReset(?string $token): void
    {
        if ($token !== null && !$this->pwReset->isTokenValid($token)) {
            $this->flashMessage($this->translator->translate('Invalid token.'));
            $this->redirect('Homepage:');
        }
    }

    public function renderDefault(): void
    {
        $this->template->lang = $this->getLang();
    }

    public function renderReset(): void
    {
        $this->template->lang = $this->getLang();
    }

    public function getLang(): string
    {
        return $this->translator->getLang();
    }

    protected function createComponentRecoveryForm(): PasswordRecoveryForm
    {
        $form = $this->passwordRecoveryFormFactory->create();
        $form->onSuccess[] = function (UserEntry $user): void {
            $this->flashMessage($this->translator->translate("An e-mail has been sent to %s.", $user->getEmail()));
            $this->redirect('Sign:');
        };

        return $form;
    }

    protected function createComponentNewPasswordForm(): NewPasswordForm
    {
        $form = $this->newPasswordFormFactory->create($this->getParameter('token'));
        $form->onSuccess[] = function (): void {
            $this->flashMessage(
                $this->translator->translate('Password change has been successful. Now you can log in.')
            );
            $this->redirect('Homepage:');
        };

        return $form;
    }
}
