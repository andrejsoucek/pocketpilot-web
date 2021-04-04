<?php

declare(strict_types=1);

namespace PP\Controls;

use GettextTranslator\Gettext;
use Nette\Application\UI\Form;
use Nette\Security\User;
use PP\User\UserRead;
use PP\User\UserUpdate;

/**
 * @author Andrej Souček
 */
class ApiKeyForm extends BaseControl
{

    public array $onSuccess = [];

    private UserRead $read;

    private UserUpdate $update;

    private Gettext $translator;

    private User $user;

    public function __construct(UserRead $read, UserUpdate $update, Gettext $translator, User $user)
    {
        $this->read = $read;
        $this->update = $update;
        $this->translator = $translator;
        $this->user = $user;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/apiKeyForm.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->addText('email', 'E-mail')->setHtmlAttribute('readonly', 'readonly');
        $form->addText('token', 'Secret key')->setHtmlAttribute('readonly', 'readonly');
        $form->addSubmit('submit', 'Generate new key');
        $form->setDefaults($this->getDefaults());
        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    /**
     * @internal
     */
    public function processForm(): void
    {
        $user = $this->user->getIdentity();
        if ($user !== null) {
            $this->update->regenerateTokenFor($user);
            $this->onSuccess();
        }
    }

    /**
     * @return array
     * @throws \Nette\Utils\AssertionException
     */
    private function getDefaults(): array
    {
        return [
            'email' => $this->user->getIdentity()->email,
            'token' => $this->read->fetchBy($this->user->getIdentity()->email)->getToken()
        ];
    }
}

interface ApiKeyFormFactory
{
    public function create(): ApiKeyForm;
}
