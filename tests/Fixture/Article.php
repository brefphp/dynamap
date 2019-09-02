<?php declare(strict_types=1);

namespace Dynamap\Test\Fixture;

use Ramsey\Uuid\Uuid;

class Article
{
    /** @var int */
    private $id;

    /** @var string|null */
    private $name;

    /** @var float */
    private $rating = 5.;

    /** @var bool */
    private $published = false;

    /** @var int */
    private $numComments = 0;

    /** @var \DateTimeImmutable */
    private $createdAt;

    /** @var \DateTimeImmutable|null */
    private $publishedAt;

    private $authorComment;

    public function __construct($id = null)
    {
        if ($id === null) {
            $id = Uuid::uuid4();
        }
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable;
    }

    public function getId()
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
        $this->publishedAt = new \DateTimeImmutable;
    }

    public function unpublish()
    {
        $this->published = false;
        $this->publishedAt = null;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function getCreationDate(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setPublicationDate(?\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setNumComments(int $numComments): void
    {
        $this->numComments = $numComments;
    }

    public function getNumComments(): ?int
    {
        return $this->numComments;
    }

    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }

    public function setAuthorComment(string $comment): void
    {
        $this->authorComment = $comment;
    }

    public function getAuthorComment(): ?string
    {
        return $this->authorComment;
    }
}
