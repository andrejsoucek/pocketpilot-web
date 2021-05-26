<?php

declare(strict_types=1);

namespace PP\HCaptcha;

use Nette\Http\Request;
use Nette\Utils\Json;

class HCaptchaVerifier
{

    private string $url;

    private string $secret;

    private string $sitekey;

    private Request $request;

    public function __construct(string $url, string $secret, string $sitekey, Request $request)
    {
        $this->url = $url;
        $this->secret = $secret;
        $this->sitekey = $sitekey;
        $this->request = $request;
    }

    public function verify(string $token): bool
    {
        $responseStr = self::post(
            $this->url,
            $this->secret,
            $token,
            $this->sitekey,
            $this->request->getRemoteAddress()
        );
        $response = Json::decode($responseStr, Json::FORCE_ARRAY);

        return $response['success'] ?? false;
    }

    private static function post(string $url, string $secret, string $token, string $sitekey, ?string $ip): string
    {
        $data = [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $ip,
            'sitekey' => $sitekey,
        ];
        $postData = http_build_query($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /** @var string|false $response */
        $response = curl_exec($ch);
        if ($response === false) {
            return '{}';
        }

        return $response;
    }
}
