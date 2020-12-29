<?php declare(strict_types=1);

namespace Dynamap\Test\Fixture;

class Article
{
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
