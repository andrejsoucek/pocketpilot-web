<?php

declare(strict_types=1);

namespace PP\API;

use Nette\Application\AbortException;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Template;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;
use PP\POI\POIRead;

/**
 * @author Andrej Souček
 */
class POIPresenter extends Presenter
{
    private POIRead $read;

    public function __construct(POIRead $read)
    {
        parent::__construct();
        $this->read = $read;
    }

    /**
     * @throws AbortException
     */
    public function actionRead(string $lng, string $lat, int $range = 500, ?bool $hasRunway = null): void
    {
        $this->assertCoordinates($lng, $lat);
        $this->assertRange($range);
        $point = $this->read->fetchClosestPoiTo($lng, $lat, $range, $hasRunway);
        if ($point) {
            $latlng = new \stdClass();
            $latlng->lat = $point->getLatitude();
            $latlng->lon = $point->getLongitude();
            $x = new \stdClass();
            $x->name = $point->getName();
            $x->description = $point->getDescription();
            $x->center = $latlng;
            $this->sendResponse(new JsonResponse($x));
        }
        $this->sendResponse(new JsonResponse(new \stdClass()));
    }

    /**
     * @throws AbortException
     */
    private function assertRange(int $range): void
    {
        if ($range > 100000) {
            $this->getHttpResponse()->setCode(400);
            $this->sendResponse(new JsonResponse(['error' => 'Range must be lower than 100 000 m.']));
        }
    }

    /**
     * @throws AbortException
     */
    private function assertCoordinates(string $lng, string $lat): void
    {
        try {
            Validators::assert($lng, 'numeric');
            Validators::assert($lat, 'numeric');
        } catch (AssertionException $e) {
            $this->getHttpResponse()->setCode(400);
            $this->sendResponse(new JsonResponse(['error' => 'Bad coordinates']));
        }
    }

    public function sendTemplate(?Template $template = null): void
    {
    }
}
