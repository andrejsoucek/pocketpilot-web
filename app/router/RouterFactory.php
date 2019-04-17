<?php

declare(strict_types=1);

namespace PP;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory {

	use Nette\StaticClass;

	public static function createRouter(): RouteList {
		$router = new RouteList;
		$router->addRoute('[<lang=cs cs|en>/]<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}
}
