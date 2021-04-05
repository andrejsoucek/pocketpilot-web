<?php

declare(strict_types=1);

namespace PP\Controls;

use GettextTranslator\Gettext;
use Nette\Application\UI\Form;
use PP\HCaptcha\HCaptchaVerifier;
use PP\IncorrectCredentialsException;
use PP\SignModel;

/**
 * @author Andrej Souček
 */
class RegisterForm extends BaseControl
{

    public array $onSuccess = [];

    private SignModel $model;

    private Gettext $translator;

    private HCaptchaVerifier $hCaptchaVerifier;

    private string $hCaptchaSiteKey;

    public function __construct(
        SignModel $model,
        Gettext $translator,
        HCaptchaVerifier $hCaptchaVerifier,
        string $hCaptchaSiteKey
    ) {
        $this->hCaptchaSiteKey = $hCaptchaSiteKey;
        $this->model = $model;
        $this->hCaptchaVerifier = $hCaptchaVerifier;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/registerForm.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->addText('username', 'Username')
            ->setRequired('Please fill in your username.')
            ->setHtmlAttribute('placeholder', 'Username');
        $form->addText('email', 'E-mail')
            ->setRequired('Please fill in an e-mail.')
            ->addRule($form::EMAIL, 'The e-mail must be in correct format.')
            ->setHtmlAttribute('placeholder', 'E-mail');
        $form->addPassword('password', 'Password')
            ->setRequired('Please fill in both of the password fields.')
            ->setHtmlAttribute('placeholder', 'Password');
        $form->addPassword('password_confirm', 'Password again')
            ->addRule(Form::EQUAL, 'Passwords do not match', $form['password'])
            ->setRequired('Please fill in both of the password fields.')
            ->setHtmlAttribute('placeholder', 'Password again')
            ->setOmitted(true);
        $form->addSubmit('send', 'Submit');
        $form->addHCaptcha($this->hCaptchaSiteKey);
        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    /**
     * @internal
     */
    public function processForm(Form $form): void
    {
        $values = $form->getValues();
        try {
            $isHuman = $this->hCaptchaVerifier->verify($values->hCaptchaResponse);
            if ($isHuman) {
                $this->model->registerUser($values->username, $values->email, null, $values->password);
                $this->onSuccess();
            } else {
                $form->addError($this->translator->translate("Incorrect captcha, please try again."));
            }
        } catch (IncorrectCredentialsException $e) {
            $form->addError($this->translator->translate('This account already exists.'));
        }
    }
}

interface RegisterFormFactory
{
    public function create(string $hCaptchaSiteKey): RegisterForm;
}
