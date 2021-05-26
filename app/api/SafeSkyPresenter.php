<?php

declare(strict_types=1);

namespace PP\API;

use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use PP\SafeSky\SafeSkyRead;

class SafeSkyPresenter extends Presenter
{
    private SafeSkyRead $safeSkyApi;

    public function __construct(SafeSkyRead $safeSkyApi)
    {
        parent::__construct();
        $this->safeSkyApi = $safeSkyApi;
    }

    public function actionRead(string $viewport): void
    {
        $safeSkyResponse = $this->safeSkyApi->fetchFor($viewport);
        $this->getHttpResponse()->setContentType('application/json');
        $this->getHttpResponse()->setCode($safeSkyResponse->getStatusCode());
        $this->sendResponse(new TextResponse($safeSkyResponse->getData()));
    }

    public function sendTemplate(): void
    {
    }
}
