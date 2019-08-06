<?php declare(strict_types=1);

namespace Dynamap\Test\Mapping;

use Dynamap\Mapping\Exception\NoTableSpeficiedException;
use Dynamap\Mapping\Mapping;
use Dynamap\Test\Fixture\Article;
use Dynamap\Test\Fixture\Author;
use Dynamap\Test\Fixture\Tag;
use PHPUnit\Framework\TestCase;

class MappingTest extends TestCase
{
    public function test an exception is thrown when no tables are specified(): void
    {
        $this->expectException(NoTableSpeficiedException::class);
        Mapping::fromConfigArray([]);
    }

    public function test an exception is thrown when tables array is empty(): void
    {
        $this->expectException(NoTableSpeficiedException::class);
        Mapping::fromConfigArray([
            'tables' => [],
        ]);
    }

    public function test a table mapping is created(): void
    {
        $mapping = Mapping::fromConfigArray([
            'tables' => [
                [
                    'name' => 'my_table',
                    'mappings' => [
                        Article::class => [],
                        Author::class => [],
                    ],
                ],
                [
                    // if you're thinking about using mulitple tables, go back and read the AWS docs on why you shouldn't.
                    // then (and only then) come back and think about if you actually _really_ do want to do this.
                    'name' => 'other_table',
                    'mappings' => [
                        Tag::class => []
                    ]
                ]
            ],
        ]);

        $this->assertSame('my_table', $mapping->getTableFor(Article::class));
        $this->assertSame('my_table', $mapping->getTableFor(Author::class));
        $this->assertSame('other_table', $mapping->getTableFor(Tag::class));
    }

    public function test a property mapping status can be queried()
    {
        $mapping = Mapping::fromConfigArray([
            'tables' => [
                [
                    'name' => 'my_table',
                    'mappings' => [
                        Article::class => [
                            'fields' => [
                                'id' => 'integer'
                            ]
                        ]
                    ],
                ]
            ],
        ]);

        $this->assertTrue($mapping->isClassPropertyMapped(Article::class, 'id'));
        $this->assertFalse($mapping->isClassPropertyMapped(Article::class, 'other_field'));
    }
}
