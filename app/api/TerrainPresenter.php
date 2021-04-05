<?php

declare(strict_types=1);

namespace PP\API;

use Nette\Application\AbortException;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Template;
use PP\Terrain\TerrainEntry;
use PP\Terrain\TerrainRead;

/**
 * @author Andrej Souček
 */
class TerrainPresenter extends Presenter
{
    private TerrainRead $read;

    public function __construct(TerrainRead $read)
    {
        parent::__construct();
        $this->read = $read;
    }

    /**
     * @throws AbortException
     */
    public function actionRead(string $path): void
    {
        $latlngs = array_map(static function ($s) {
            return explode(',', $s);
        }, explode('|', $path));
        if (count($latlngs) < 2) {
            $this->getHttpResponse()->setCode(400);
            $this->sendResponse(new JsonResponse(['error' => 'Path must contain at least two points.']));
        }
        $terrain = $this->read->fetchTerrain($latlngs);
        $xs = [];
        /** @var TerrainEntry $point */
        foreach ($terrain as $point) {
            $x = new \stdClass();
            $x->relativeDistance = $point->getRelativeDistance();
            $x->elevation = $point->getElevationFeet();
            $xs[] = $x;
        }

        $this->sendResponse(new JsonResponse($xs));
    }

    public function sendTemplate(?Template $template = null): void
    {
    }
}
