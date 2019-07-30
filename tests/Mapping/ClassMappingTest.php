<?php

namespace Dynamap\Test\Mapping;

use Dynamap\Mapping\ClassMapping;
use Dynamap\Mapping\Exception\ClassNameInvalidException;
use Dynamap\Test\Fixture\Article;
use PHPUnit\Framework\TestCase;

class ClassMappingTest extends TestCase
{
    public function test an exception is thrown when the fqcn is invalid(): void
    {
        $this->expectException(ClassNameInvalidException::class);
        ClassMapping::fromArray(Article::class, []);
    }
}