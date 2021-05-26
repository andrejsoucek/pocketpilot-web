<?php

declare(strict_types=1);

namespace PP\SafeSky;

class SafeSkyResponse
{
    private string $data;

    private int $statusCode;

    public function __construct(string $data, int $statusCode)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
