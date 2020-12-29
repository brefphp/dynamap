<?php declare(strict_types=1);

namespace Dynamap\Test\Fixture;

use DateTimeImmutable;

class Annotations
{
    /** @var int */
    private $id;

    /** @var string|null */
    private $name;

    /** @var float */
    private $rating = 5.;

    /** @var bool */
    private $published = false;

    /** @var DateTimeImmutable */
    private $createdAt;

    /** @var DateTimeImmutable|null */
    private $publishedAt;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function publish(): void
    {
        $this->published = true;
        $this->publishedAt = new DateTimeImmutable;
    }

    public function unpublish(): void
    {
        $this->published = false;
        $this->publishedAt = null;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function getCreationDate(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setPublicationDate(?DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getPublicationDate(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }
}
