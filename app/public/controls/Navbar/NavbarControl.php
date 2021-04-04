<?php

declare(strict_types=1);

namespace PP\Controls;

use GettextTranslator\Gettext;

/**
 * @author Andrej Souček
 */
class NavbarControl extends BaseControl
{

    private Gettext $translator;

    public function __construct(Gettext $translator)
    {
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/navbarControl.latte');
        $this->template->setTranslator($this->translator);
        $this->template->render();
    }
}

interface NavbarControlFactory
{
    public function create(): NavbarControl;
}
