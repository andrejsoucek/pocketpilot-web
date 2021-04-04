<?php

declare(strict_types=1);

namespace PP\Dashboard;

use Nette\SmartObject;
use Nette\Utils\DateTime;

/**
 * @author Andrej Souček
 */
class DashboardEntry
{
    use SmartObject;

    private int $id;

    private string $title;

    private DateTime $created;

    private ?string $item1;

    private ?string $item2;

    private ?string $item3;

    private ?string $item4;

    public function __construct(
        int $id,
        string $title,
        DateTime $created,
        ?string $item1,
        ?string $item2,
        ?string $item3,
        ?string $item4
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->created = $created;
        $this->item1 = $item1;
        $this->item2 = $item2;
        $this->item3 = $item3;
        $this->item4 = $item4;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getItem1(): ?string
    {
        return $this->item1;
    }

    public function getItem2(): ?string
    {
        return $this->item2;
    }

    public function getItem3(): ?string
    {
        return $this->item3;
    }

    public function getItem4(): ?string
    {
        return $this->item4;
    }
}
