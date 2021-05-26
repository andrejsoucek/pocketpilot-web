<?php

declare(strict_types=1);

namespace PP\SafeSky;

class SafeSkyRead
{
    private string $url;

    private string $apiKey;

    public function __construct(string $url, string $apiKey)
    {
        $this->url = $url;
        $this->apiKey = $apiKey;
    }

    public function fetchFor(string $viewPort): SafeSkyResponse
    {
        return self::get(
            $this->url,
            $this->apiKey,
            $viewPort,
        );
    }

    private static function get(string $baseUrl, string $apiKey, string $viewPort): SafeSkyResponse
    {
        $requestUrl = sprintf('%sviewport=%s&show_grounded=true', $baseUrl, $viewPort);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "x-api-key: $apiKey",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /** @var string|false $response */
        $response = curl_exec($ch);
        if ($response === false) {
            return new SafeSkyResponse('[]', 503);
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return new SafeSkyResponse($response, $statusCode);
    }
}
