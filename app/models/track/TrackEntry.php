<?php

declare(strict_types=1);

namespace PP\Track;

use Nette\SmartObject;
use Nette\Utils\DateTime;

/**
 * @author Andrej Souček
 */
class TrackEntry
{
    use SmartObject;

    private int $id;

    private string $track;

    private float $length;

    private int $userId;

    private string $name;

    private DateTime $created;

    private string $hash;

    public function __construct(
        int $id,
        string $track,
        float $length,
        int $userId,
        string $name,
        DateTime $created,
        string $hash
    ) {
        $this->id = $id;
        $this->track = $track;
        $this->length = $length;
        $this->userId = $userId;
        $this->name = $name;
        $this->created = $created;
        $this->hash = $hash;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getTrack(): string
    {
        return $this->track;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
