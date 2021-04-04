<?php

declare(strict_types=1);

namespace PP\Presenters;

/**
 * @author Andrej Souček
 */
trait AdminAuthentication
{

    public function checkRequirements($element): void
    {
        parent::checkRequirements(static::getReflection());
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Sign:');
        }
        if (!$this->user->isInRole('admin')) {
            $this->flashMessage($this->translator->translate('Access denied.'));
            $this->redirect('Dashboard:default');
        }
    }
}
