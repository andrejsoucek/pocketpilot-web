<?php

declare(strict_types=1);

namespace PP\Airspace;

class RelativePosition
{
    private float $in;

    private float $out;

    public function __construct(float $in, float $out)
    {
        $this->in = $in;
        $this->out = $out <= $in ? 1 : $out;
    }

    public function getIn(): float
    {
        return $this->in;
    }

    public function getOut(): float
    {
        return $this->out;
    }
}
