<?php

declare(strict_types=1);

namespace PP\Controls;

use GettextTranslator\Gettext;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Security\User;

/**
 * @author Andrej Souček
 */
class ContactForm extends BaseControl
{

    public array $onSuccess = [];

    private IMailer $mailer;

    private Gettext $translator;

    private User $user;

    public function __construct(IMailer $mailer, Gettext $translator, User $user)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->user = $user;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/contactForm.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->addTextArea('message', 'Your message')
            ->setRequired('Please enter the message.')
            ->setHtmlAttribute('placeholder', 'Your message');
        $form->addSubmit('send', 'Send');
        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    /**
     * @internal
     * @param Form $form
     */
    public function processForm(Form $form): void
    {
        $values = $form->getValues();
        $this->mailer->send($this->createMail($values->message));
        $this->onSuccess();
    }

    private function createMail(string $s): Message
    {
        $message = new Message();
        $message->addTo('andrejsoucek@gmail.com'); //@TODO do cfg
        $message->setFrom('info@pocketpilot.cz');
        $message->setSubject('Zpráva z PocketPilot.cz');
        $message->setBody($s . "\n\n" . $this->user->getIdentity()->email);
        return $message;
    }
}

interface ContactFormFactory
{
    public function create(): ContactForm;
}
