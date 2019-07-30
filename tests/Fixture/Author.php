<?php
declare(strict_types=1);

namespace Dynamap\Test\Fixture;

class Author
{
    /** @var Uuid */
    private $id;

    /** @var string */
    private $name;

    /** @var Article[] */
    private $articles = [];

    /**
     * Author constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

//    public function addArticle(Article $article)
//    {
//        $article->setAuthor($this);
//        $this->articles[] = $article;
//    }
//
//    public function getArticles(): array
//    {
//        return $this->articles;
//    }
}
