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
        ClassMapping::fromArray('UnknownClass', []);
    }

    public function test fields are mapped(): void
    {
        $mapping = [
            'fields' => [
                'id' => 'uuid',
                'name' => 'string',
                'createdAt' => 'datetime',
                'rating' => 'float',
                'numComments' => 'integer'
            ]
        ];

        $classMapping = ClassMapping::fromArray(Article::class, $mapping);
    }
}