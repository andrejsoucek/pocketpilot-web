<?php

declare(strict_types=1);

namespace PP\Controls;

use GettextTranslator\Gettext;
use Nette\Application\UI\Form;
use Nette\Http\Session;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use PP\HCaptcha\HCaptchaVerifier;
use PP\User\PasswordCredentials;

/**
 * @author Andrej Souček
 */
class LoginForm extends BaseControl
{
    public array $onSuccess = [];

    private Session $session;

    private Gettext $translator;

    private User $user;

    private string $hCaptchaSiteKey;

    private const INVALID_LOGIN_ATTEMPTS_SESSION_SECTION = 'loginAttempts';
    private const INVALID_LOGIN_ATTEMPTS_SESSION_KEY = 'attempt';
    private const INVALID_LOGIN_ATTEMPTS_THRESHOLD = 3;

    public function __construct(
        Session $session,
        Gettext $translator,
        User $user,
        string $hCaptchaSiteKey
    ) {
        $this->session = $session;
        $this->translator = $translator;
        $this->user = $user;
        $this->hCaptchaSiteKey = $hCaptchaSiteKey;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/loginForm.latte');
        $this->template->setTranslator($this->translator);
        $this->template->lang = $this->getLang();
        $this->template->render();
    }

    public function getLang(): string
    {
        return $this->translator->getLang();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->addText('email', 'E-mail')
            ->setRequired('Please enter your e-mail.')
            ->addRule($form::EMAIL, 'The e-mail must be in correct format.')
            ->setHtmlAttribute('placeholder', 'E-mail');
        $form->addPassword('password', 'Password')
            ->setRequired('Please enter your password.')
            ->setHtmlAttribute('placeholder', 'Password');
        $form->addSubmit('send', 'Log in');
        if ($this->getInvalidLoginAttempts() >= self::INVALID_LOGIN_ATTEMPTS_THRESHOLD) {
            $form->addHCaptcha($this->hCaptchaSiteKey);
        }
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
            $this->user->login(new PasswordCredentials($values->email, $values->password));
            $this->resetInvalidLoginAttempts();
            $this->onSuccess();
        } catch (AuthenticationException $e) {
            $this->incrementInvalidLoginAttempts();
            $form->addError($this->translator->translate("Incorrect e-mail or password."));
        }
    }

    private function getInvalidLoginAttempts(): int
    {
        $section = $this->session->getSection(self::INVALID_LOGIN_ATTEMPTS_SESSION_SECTION);
        return $section->offsetExists(self::INVALID_LOGIN_ATTEMPTS_SESSION_KEY) ?
            $section->offsetGet(self::INVALID_LOGIN_ATTEMPTS_SESSION_KEY) :
            1;
    }

    private function resetInvalidLoginAttempts(): void
    {
        $section = $this->session->getSection(self::INVALID_LOGIN_ATTEMPTS_SESSION_SECTION);
        $section->offsetSet(self::INVALID_LOGIN_ATTEMPTS_SESSION_KEY, 1);
    }

    private function incrementInvalidLoginAttempts(): void
    {
        $section = $this->session->getSection(self::INVALID_LOGIN_ATTEMPTS_SESSION_SECTION);
        $v = $this->getInvalidLoginAttempts() + 1;
        $section->offsetSet(self::INVALID_LOGIN_ATTEMPTS_SESSION_KEY, $v);
    }
}

interface LoginFormFactory
{
    public function create(string $hCaptchaSiteKey): LoginForm;
}
