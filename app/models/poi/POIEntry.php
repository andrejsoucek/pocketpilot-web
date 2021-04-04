<?php

declare(strict_types=1);

namespace PP\POI;

use Nette\SmartObject;

/**
 * @author Andrej Souček
 */
class POIEntry
{
    use SmartObject;

    private string $name;

    private string $description;

    private string $longitude;

    private string $latitude;

    public function __construct(string $name, string $description, string $longitude, string $latitude)
    {
        $this->name = $name;
        $this->description = $description;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }
}
