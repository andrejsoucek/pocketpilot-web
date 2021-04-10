<?php

declare(strict_types=1);

namespace PP\Controls;

use GettextTranslator\Gettext;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Utils\AssertionException;
use PP\IncorrectCredentialsException;
use PP\User\PasswordReset;
use PP\User\UserRead;

/**
 * @author Andrej Souček
 */
class PasswordRecoveryForm extends BaseControl
{
    private string $hCaptchaSiteKey;

    public array $onSuccess = [];

    private UserRead $userRead;

    private PasswordReset $pwReset;

    private Gettext $translator;

    public function __construct(
        string $hCaptchaSiteKey,
        UserRead $userRead,
        PasswordReset $pwReset,
        Gettext $translator
    ) {
        $this->hCaptchaSiteKey = $hCaptchaSiteKey;
        $this->userRead = $userRead;
        $this->pwReset = $pwReset;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/passwordRecoveryForm.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->addText('email', 'E-mail')
            ->setRequired('Please enter your e-mail.')
            ->addRule($form::EMAIL, 'The e-mail must be in correct format.')
            ->setHtmlAttribute('placeholder', 'E-mail');
        $form->addSubmit('send', 'Reset password');
        $form->addHCaptcha($this->hCaptchaSiteKey);

        $form->onSuccess[] = array($this, 'processForm');
        return $form;
    }

    /**
     * @param Form $form
     * @throws InvalidLinkException
     * @throws AssertionException
     * @internal
     */
    public function processForm(Form $form): void
    {
        try {
            $user = $this->userRead->fetchBy($form->values->email);
            $this->pwReset->sendLinkTo($user);
            $this->onSuccess($user);
        } catch (IncorrectCredentialsException $e) {
            $form->addError($this->translator->translate('E-mail not found. Please sign up.'));
        }
    }
}

interface PasswordRecoveryFormFactory
{
    public function create(string $hCaptchaSiteKey): PasswordRecoveryForm;
}
