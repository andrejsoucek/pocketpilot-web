<?php

declare(strict_types=1);

namespace PP;

use Contributte\ApiRouter\ApiRoute;
use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList();
        $router->add(new ApiRoute('/api/v1/login', 'APIv1:Login'));
        $router->add(new ApiRoute('/api/v1/tracks', 'APIv1:Tracks'));
        $router->add(new ApiRoute('/api/v1/poi', 'APIv1:POI'));
        $router->add(new ApiRoute('/api/v1/airspace', 'APIv1:Airspace'));
        $router->add(new ApiRoute('/api/v1/terrain', 'APIv1:Terrain'));
        $router->add(new ApiRoute('/api/v1/safesky', 'APIv1:SafeSky'));
        $router->addRoute('[<lang=cs cs|en>/]share/?t=<id>', 'Share:default', $router::ONE_WAY);
        $router->addRoute('[<lang=cs cs|en>/]share/<id>', 'Share:default');
        $router->addRoute('[<lang=cs cs|en>/]<presenter>/<action>[/<id>]', 'Homepage:default');
        return $router;
    }
}
