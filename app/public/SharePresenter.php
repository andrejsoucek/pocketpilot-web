<?php

declare(strict_types=1);

namespace PP\Presenters;

use GettextTranslator\Gettext;
use PP\DirResolver;
use PP\Track\TrackRead;

/**
 * @author Andrej Souček
 */
class SharePresenter extends AppPresenter
{

    private TrackRead $read;

    public function __construct(TrackRead $read)
    {
        parent::__construct();
        $this->read = $read;
    }

    public function renderDefault(string $id): void
    {
        $this->template->trackJson = $this->getTrackJson($id);
    }

    public function getTrackJson(string $hash): string
    {
        $track = $this->read->fetchByHash($hash);
        if ($track === null) {
            $this->flashMessage($this->translator->translate('Track does not exist.'));
            $this->redirect('Homepage:');
        }

        return $track->getTrack();
    }
}
