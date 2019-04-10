<?php

namespace PP\Presenters;

use Nette\Application\UI\Presenter;
use PP\Track\TrackEntry;
use PP\Track\TrackRead;
use PP\UserLoggedIn;

/**
 * @author Andrej Souček
 * @User
 */
class TracksPresenter extends Presenter {

	use UserLoggedIn;

	/**
	 * @var TrackRead
	 */
	private $read;

	/**
	 * @var TrackEntry[]
	 */
	private $tracks;

	public function __construct(TrackRead $read) {
		parent::__construct();
		$this->read = $read;
	}

	public function renderDefault() {
		$this->template->tracks = $this->getTracks();
	}

	public function renderMap($id) {
		bdump($this->getTracks()[$id]);
		$this->template->hideNavbar = true;
	}

	private function getTracks() {
		if (empty($tracks)) {
			$this->tracks = $this->read->fetchBy($this->user->getId());
		}
		return $this->tracks;
	}
}
