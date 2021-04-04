<?php

declare(strict_types=1);

namespace PP\Terrain;

use Nette\SmartObject;

class TerrainEntry
{
    use SmartObject;

    private float $relativeDistance;

    private float $elevation;

    public function __construct(float $relativeDistance, float $elevation)
    {
        $this->relativeDistance = $relativeDistance;
        $this->elevation = $elevation;
    }

    public function getRelativeDistance(): float
    {
        return $this->relativeDistance;
    }

    public function getElevationMetres(): float
    {
        return $this->elevation;
    }

    public function getElevationFeet(): float
    {
        return self::mToFt($this->elevation);
    }

    private static function mToFt(float $elev): float
    {
        return round($elev * 3.2808399);
    }
}
