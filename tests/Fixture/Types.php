<?php declare(strict_types=1);

namespace Dynamap\Test\Fixture;

use DateTimeImmutable;

class Types
{
    public int $id;
    public ?string $name = null;
    public float $rating = 5.;
    public bool $published = false;
    public DateTimeImmutable $createdAt;
    public ?DateTimeImmutable $publishedAt = null;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable;
    }

    public function publish(): void
    {
        $this->published = true;
        $this->publishedAt = new DateTimeImmutable;
    }

    public function unpublish()
    {
        $this->published = false;
        $this->publishedAt = null;
    }
}
