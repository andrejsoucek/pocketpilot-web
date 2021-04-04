<?php

declare(strict_types=1);

namespace PP\Controls;

use GettextTranslator\Gettext;
use Nette\Application\UI\Form;
use PP\User\PasswordReset;

/**
 * @author Andrej Souček
 */
class NewPasswordForm extends BaseControl
{

    public array $onSuccess = [];

    private PasswordReset $pwReset;

    private Gettext $translator;

    private string $token;

    public function __construct(PasswordReset $pwReset, Gettext $translator, string $token)
    {
        $this->pwReset = $pwReset;
        $this->translator = $translator;
        $this->token = $token;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/newPasswordForm.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->addPassword('password', 'Password')
            ->setRequired('Please fill in both of the password fields.')
            ->setHtmlAttribute('placeholder', 'Password');
        $form->addPassword('password_confirm', 'Password again')
            ->addRule(Form::EQUAL, 'Passwords do not match', $form['password'])
            ->setRequired('Please fill in both of the password fields.')
            ->setHtmlAttribute('placeholder', 'Password again')
            ->setOmitted(true);
        $form->addSubmit('send', 'Submit');

        $form->onSuccess[] = array($this, 'processForm');
        return $form;
    }

    /**
     * @internal
     */
    public function processForm(Form $form): void
    {
        $this->pwReset->changePassword($this->token, $form->values->password); // neplatný token vyřešen už v action
        $this->onSuccess();
    }
}

interface NewPasswordFormFactory
{
    public function create(string $token): NewPasswordForm;
}
