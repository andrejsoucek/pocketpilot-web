<?php

declare(strict_types=1);

namespace PP\Controls;

use GettextTranslator\Gettext;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Nette\Utils\AssertionException;
use PP\Track\TrackCreate;
use PP\Track\TrackEntry;
use PP\Track\TrackRead;
use PP\Track\TrackUpdate;

/**
 * @author Andrej Souček
 */
class SaveTrackForm extends BaseControl
{

    public array $onSuccess = [];

    public array $onError = [];

    private TrackCreate $create;

    private TrackUpdate $update;

    private Gettext $translator;

    private User $user;

    private ?TrackEntry $track;

    public function __construct(
        TrackCreate $create,
        TrackUpdate $update,
        Gettext $translator,
        User $user,
        ?TrackEntry $track
    ) {
        $this->create = $create;
        $this->update = $update;
        $this->translator = $translator;
        $this->user = $user;
        $this->track = $track;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/saveTrackForm.latte');
        $this->template->setTranslator($this->translator);
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->addText('name', 'Track name')
            ->addRule(Form::REQUIRED, 'Please fill in the name.')
            ->addRule(Form::MAX_LENGTH, 'The name cannot be longer than %d letters.', 50);
        $form->addHidden('waypoints');
        $form->addHidden('trackId');
        $form->addSubmit('save', 'Save');
        $form->setDefaults($this->getDefaults());
        $form->onSuccess[] = [$this, 'processForm'];
        return $form;
    }

    /**
     * @internal
     * @throws AssertionException
     */
    public function processForm(Form $form): void
    {
        $values = $form->getValues();
        $wpts = json_decode($values->waypoints, true);
        if (empty($wpts) || count($wpts) <= 1) {
            $this->onError($this->translator->translate('Track must have more than 1 point'));
        } else {
            try {
                if ($values->trackId) {
                    $this->update->process((int)$values->trackId, $values->name, $wpts);
                } else {
                    $this->create->process($values->name, $this->user->getId(), $wpts);
                }
                $this->onSuccess($values->name);
            } catch (\RuntimeException $e) {
                $this->onError($this->translator->translate('An error occurred while saving the track.'));
            }
        }
    }

    private function getDefaults(): array
    {
        if ($this->track) {
            return [
                'trackId' => $this->track->getId(),
                'name' => $this->track->getName()
            ];
        }
        return [];
    }
}

interface SaveTrackFormFactory
{
    public function create(?TrackEntry $track): SaveTrackForm;
}
