<?php

declare(strict_types=1);

namespace PP\Presenters;

use GettextTranslator\Gettext;
use PP\DirResolver;
use PP\Track\TrackRead;

/**
 * @author Andrej Souček
 */
class SharePresenter extends AppPresenter {

	/**
	 * @var TrackRead
	 */
	private $read;

	public function __construct(DirResolver $dirResolver, Gettext $translator, TrackRead $read) {
		parent::__construct($dirResolver, $translator);
		$this->read = $read;
	}

	public function renderDefault(string $hash): void {
		$this->template->trackJson = $this->getTrackJson($hash);
	}

	public function getTrackJson(string $hash): string {
		$track = $this->read->fetchByHash($hash);
		return $track ? $track->getTrack() : '';
	}
}
