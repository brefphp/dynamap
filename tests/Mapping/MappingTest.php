<?php declare(strict_types=1);

namespace Dynamap\Test\Mapping;

use Dynamap\Mapping\Exception\NoTableSpeficiedException;
use Dynamap\Mapping\Mapping;
use Dynamap\Test\Fixture\Article;
use Dynamap\Test\Fixture\Author;
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
            ],
        ]);

//        $this->assertSame('my_table', $mapping->getTableFor(Article::class));
    }
}
