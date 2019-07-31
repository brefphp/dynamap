<?php declare(strict_types=1);

namespace Dynamap\Test\Mapping;

use Dynamap\Mapping\Exception\MappingNotSpecifiedException;
use Dynamap\Mapping\Exception\TableNameNotSpecifiedException;
use Dynamap\Mapping\TableMapping;
use Dynamap\Test\Fixture\Article;
use PHPUnit\Framework\TestCase;

class TableMappingTest extends TestCase
{
    public function test an exception is thrown when table name is not specified(): void
    {
        $this->expectException(TableNameNotSpecifiedException::class);
        TableMapping::fromArray([]);
    }

    public function test an exception is thrown when no mappings are specified(): void
    {
        $this->expectException(MappingNotSpecifiedException::class);
        TableMapping::fromArray(
            [
                'name' => 'my_table',
            ]
        );
    }

    public function test a valid mapping(): void
    {
        $mapping = TableMapping::fromArray([
            'name' => 'my_table',
            'mappings' => [
                Article::class => [],
            ],
        ]);

        $this->assertSame('my_table', $mapping->getTableName());
    }
}
