<?php

declare(strict_types=1);

namespace PP\Controls;

use GettextTranslator\Gettext;
use PP\POI\POIRead;

/**
 * @author Andrej Souček
 */
class POIControl extends BaseControl
{
    private POIRead $read;

    private Gettext $translator;

    public function __construct(POIRead $read, Gettext $translator)
    {
        $this->read = $read;
        $this->translator = $translator;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/poiControl.latte');
        $this->template->setTranslator($this->translator);
        $this->template->poi = $this->getPOI();
        $this->template->render();
    }

    public function getPOI(): array
    {
        return $this->read->fetchAll();
    }
}

interface POIControlFactory
{
    public function create(): POIControl;
}
