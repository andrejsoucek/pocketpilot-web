<?php

declare(strict_types=1);

namespace PP\Presenters;

use GettextTranslator\Gettext;
use PP\Dashboard\DashboardRead;
use PP\DirResolver;

/**
 * @author Andrej Souček
 */
class DashboardPresenter extends AppPresenter
{
    use Authentication;
    use Navbar;

    private DashboardRead $read;

    public function __construct(DashboardRead $read)
    {
        parent::__construct();
        $this->read = $read;
    }

    public function renderDefault(): void
    {
        $this->template->items = $this->getItems();
    }

    public function getItems(): array
    {
        return $this->read->fetchAll();
    }
}
