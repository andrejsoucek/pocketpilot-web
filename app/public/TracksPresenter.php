<?php

declare(strict_types=1);

namespace PP\Presenters;

use GettextTranslator\Gettext;
use Nette\Application\UI\Form;
use Nette\UnexpectedValueException;
use PP\Controls\SaveTrackForm;
use PP\Controls\SaveTrackFormFactory;
use PP\DirResolver;
use PP\Track\TrackCreate;
use PP\Track\TrackDelete;
use PP\Track\TrackEntry;
use PP\Track\TrackRead;
use PP\Track\TrackUpdate;

/**
 * @author Andrej Souček
 */
class TracksPresenter extends AppPresenter
{
    use Authentication;
    use Navbar;

    private TrackRead $read;

    /**
     * Lazy getter
     * @var TrackEntry[]
     */
    private array $tracks;

    private TrackDelete $delete;

    private SaveTrackFormFactory $saveTrackFormFactory;

    public function __construct(
        TrackRead $read,
        TrackDelete $delete,
        SaveTrackFormFactory $saveTrackFormFactory
    ) {
        parent::__construct();
        $this->read = $read;
        $this->delete = $delete;
        $this->saveTrackFormFactory = $saveTrackFormFactory;
    }

    public function renderDefault(): void
    {
        $this->template->tracks = $this->getTracks();
        $this->template->maximum = $this->getMaximum();
    }

    public function renderNavlog(?int $id): void
    {
        if (
            $id !== null &&
            isset($this->getTracks()[$id]) &&
            $this->getTracks()[$id]->getUserId() === $this->getUser()->getId()
        ) {
            $this->template->trackJson = $this->getTracks()[$id]->getTrack();
        } else {
            $this->redirect('Tracks:');
        }
    }

    public function getTracks(): array
    {
        if (empty($this->tracks)) {
            $this->tracks = $this->read->fetchForUser($this->user->getId());
        }
        return $this->tracks;
    }

    public function renderMap(?int $id): void
    {
        if ($id !== null) {
            if (
                isset($this->getTracks()[$id]) &&
                $this->getTracks()[$id]->getUserId() === $this->getUser()->getId()
            ) {
                $this->template->trackJson = $this->getTracks()[$id]->getTrack();
            } else {
                $this->redirect('Tracks:');
            }
        } else {
            $this->template->trackJson = null;
        }
    }

    public function handleDelete(int $id, string $name): void
    {
        try {
            $this->delete->process($id, $this->user->getId());
            $this->flashMessage($this->translator->translate("Track '%s' has been deleted.", $name));
        } catch (\RuntimeException $e) {
            $this->flashMessage($this->translator->translate("An error occurred while deleting the track."));
        }
        $this->redrawControl();
    }

    public function getMaximum(): int
    {
        switch ($this->user->getRoles()[0]) {
            case 'admin':
            case 'premium':
                return 100;
            case 'user':
                return 5;
            default:
                throw new UnexpectedValueException('Unknown role.');
        }
    }

    protected function createComponentForm(): SaveTrackForm
    {
        $id = $this->getParameter('id');
        $form = $this->saveTrackFormFactory->create($id ? $this->getTracks()[$id] : null);
        $form->onSuccess[] = function (string $trackName): void {
            $this->flashMessage($this->translator->translate("Track '%s' has been saved.", $trackName));
            $this->redirect('Tracks:');
        };
        $form->onError[] = function (string $message): void {
            $this->flashMessage($message);
            $this->redrawControl();
        };

        return $form;
    }
}
