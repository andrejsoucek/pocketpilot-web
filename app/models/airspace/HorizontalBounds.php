<?php

declare(strict_types=1);

namespace PP\Airspace;

use ArrayObject;

/**
 * @extends ArrayObject<int, RelativePosition>
 */
class HorizontalBounds extends ArrayObject
{

    /**
     * @param array<RelativePosition> $relativePositions
     */
    public function __construct(array $relativePositions)
    {
        parent::__construct($relativePositions);
    }

    public static function createFrom(array $rows): self
    {
        $positions = [];
        foreach ($rows as $row) {
            $positions[] = new RelativePosition(
                (float)$row->offsetGet(AirspaceDatabaseDef::ALIAS_START),
                (float)$row->offsetGet(AirspaceDatabaseDef::ALIAS_END)
            );
        }
        return new self($positions);
    }
}
