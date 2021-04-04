<?php

declare(strict_types=1);

namespace PP\Airspace;

use Nette\SmartObject;

class VerticalBounds
{
    use SmartObject;

    private int $lowerBound;

    private string $lowerBoundDatum;

    private int $upperBound;

    private string $upperBoundDatum;

    public function __construct(int $lowerBound, string $lowerBoundDatum, int $upperBound, string $upperBoundDatum)
    {
        $this->lowerBound = $lowerBound;
        $this->lowerBoundDatum = $lowerBoundDatum;
        $this->upperBound = $upperBound;
        $this->upperBoundDatum = $upperBoundDatum;
    }

    public function getLowerBound(): int
    {
        return $this->lowerBound;
    }

    public function getLowerBoundDatum(): string
    {
        return $this->lowerBoundDatum;
    }

    public function getUpperBound(): int
    {
        return $this->upperBound;
    }

    public function getUpperBoundDatum(): string
    {
        return $this->upperBoundDatum;
    }
}
