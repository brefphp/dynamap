<?php declare(strict_types=1);

namespace Dynamap\Test\Fixture;

class Article
{
    /** @var int */
    private $id;

    /** @var string|null */
    private $name;

    /** @var bool */
    private $published = false;

    public function __construct(int $id)
    {
        $this->id = $id;
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

    public function publish(): void
    {
        $this->published = true;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }
}
