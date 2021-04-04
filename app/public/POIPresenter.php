<?php

declare(strict_types=1);

namespace PP\Presenters;

use PP\Controls\POIControl;
use PP\Controls\POIControlFactory;
use PP\Controls\POIImportForm;
use PP\Controls\POIImportFormFactory;

/**
 * @author Andrej Souček
 */
class POIPresenter extends AppPresenter
{
    use AdminAuthentication;
    use Navbar;

    private POIImportFormFactory $formFactory;

    private POIControlFactory $POIControlFactory;

    public function __construct(
        POIImportFormFactory $formFactory,
        POIControlFactory $POIControlFactory
    ) {
        parent::__construct();
        $this->formFactory = $formFactory;
        $this->POIControlFactory = $POIControlFactory;
    }

    protected function createComponentForm(): POIImportForm
    {
        $c = $this->formFactory->create();
        $c->onError[] = function ($m): void {
            $this->flashMessage($m);
        };

        return $c;
    }

    protected function createComponentPoi(): POIControl
    {
        return $this->POIControlFactory->create();
    }
}
