<?php declare(strict_types=1);

namespace Dynamap\Test\Mapping;

use Dynamap\Mapping\ClassMapping;
use Dynamap\Mapping\Exception\CannotMapNonExistentFieldException;
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

    public function test non existent fields cannot be mapped(): void
    {
        $mapping = [
            'fields' => [
                'non_existent_field' => 'string',
            ],
        ];

        $this->expectException(CannotMapNonExistentFieldException::class);
        ClassMapping::fromArray(Article::class, $mapping);
    }

    public function test fields are mapped(): void
    {
        $mapping = [
            'fields' => [
                'id' => 'uuid',
                'name' => 'string',
                'createdAt' => 'datetime',
                'rating' => 'float',
                'numComments' => 'integer',
                'published' => 'bool'
            ],
        ];

        $classMapping = ClassMapping::fromArray(Article::class, $mapping);

        $this->assertSame('S', $classMapping->getMappedProperty('id')->getDynamoDBFieldType());
        $this->assertSame('S', $classMapping->getMappedProperty('name')->getDynamoDBFieldType());
        $this->assertSame('S', $classMapping->getMappedProperty('createdAt')->getDynamoDBFieldType());
        $this->assertSame('N', $classMapping->getMappedProperty('rating')->getDynamoDBFieldType());
        $this->assertSame('N', $classMapping->getMappedProperty('numComments')->getDynamoDBFieldType());
        $this->assertSame('BOOL', $classMapping->getMappedProperty('published')->getDynamoDBFieldType());
    }
}
