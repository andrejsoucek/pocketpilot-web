<?php

declare(strict_types=1);

namespace PP\Presenters;

use GettextTranslator\Gettext;
use Nette\Application\UI\Form;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use PP\Controls\ContactForm;
use PP\Controls\ContactFormFactory;
use PP\DirResolver;

/**
 * @author Andrej Souček
 */
class ContactPresenter extends AppPresenter
{
    use Authentication;
    use Navbar;

    private ContactFormFactory $contactFormFactory;

    public function __construct(ContactFormFactory $contactFormFactory)
    {
        parent::__construct();
        $this->contactFormFactory = $contactFormFactory;
    }

    public function createComponentForm(): ContactForm
    {
        $form = $this->contactFormFactory->create();
        $form->onSuccess[] = function () {
            $this->flashMessage($this->translator->translate('Message has been sent.'));
            $this->redirect('this');
        };
        return $form;
    }
}
