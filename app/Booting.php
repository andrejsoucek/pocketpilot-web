<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;
use Nette\Forms\Container;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Form;

class Booting
{

    public static function boot(): Configurator
    {
        $configurator = new Configurator();
        $configurator->setDebugMode(self::isDebugMode()); // enable for your remote IP
        $configurator->enableTracy(__DIR__ . '/../log');
        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory(__DIR__ . '/../temp');
        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();
        $configurator
            ->addConfig(__DIR__ . '/config/config.neon')
            ->addConfig(__DIR__ . '/config/config.local.neon');

        self::addCustomFormControls();

        return $configurator;
    }

    public static function bootForTests(): Configurator
    {
        $configurator = self::boot();
        \Tester\Environment::setup();
        return $configurator;
    }

    private static function isDebugMode(): bool
    {
        return getenv('DEBUG_MODE') === 'true';
    }

    private static function addCustomFormControls(): void
    {
        Container::extensionMethod(
            'addHCaptcha',
            function (Form $form, string $sitekey) {
                $html = $form->getElementPrototype();
                $html->addAttributes([
                    'data-hcaptcha-protected' => true,
                    'data-sitekey' => $sitekey,
                ]);
                $form->addHidden('hCaptchaResponse');

                return $form;
            }
        );
    }
}
