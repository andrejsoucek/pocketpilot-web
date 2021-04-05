<?php

declare(strict_types=1);

namespace PP\Presenters;

use Nette\Application\UI\Presenter;
use PP\Controls\WebpackControl;
use PP\Controls\WebpackControlFactory;
use GettextTranslator\Gettext;
use PP\Latte\TemplateProperty;

/**
 * @author Andrej Souček
 *
 * @property-read TemplateProperty $template
 */
abstract class AppPresenter extends Presenter
{

    /**
     * @persistent
     */
    public string $lang;

    /**
     * @inject
     */
    public Gettext $translator;

    /**
     * @inject
     */
    public WebpackControlFactory $webpackControlFactory;

    public function startup()
    {
        parent::startup();
        $this->translator->setLang($this->lang); // too late in beforeRender
    }

    public function beforeRender()
    {
        $this->template->setTranslator($this->translator);
    }

    protected function createComponentWebpack(): WebpackControl
    {
        return $this->webpackControlFactory->create();
    }
}
