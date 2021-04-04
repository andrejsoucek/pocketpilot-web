<?php

declare(strict_types=1);

namespace PP\Airspace;

use Nette\SmartObject;

/**
 * @author Andrej Souček
 */
class AirspaceEntry
{
    use SmartObject;

    private string $name;

    private string $type;

    private VerticalBounds $verticalBounds;

    private HorizontalBounds $horizontalBounds;

    public function __construct(
        string $name,
        string $type,
        VerticalBounds $verticalBounds,
        HorizontalBounds $horizontalBounds
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->verticalBounds = $verticalBounds;
        $this->horizontalBounds = $horizontalBounds;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVerticalBounds(): VerticalBounds
    {
        return $this->verticalBounds;
    }

    /**
     * @return HorizontalBounds<RelativePosition>
     */
    public function getHorizontalBounds(): HorizontalBounds
    {
        return $this->horizontalBounds;
    }
}
