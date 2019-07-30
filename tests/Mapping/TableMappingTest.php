<?php

namespace Dynamap\Test\Mapping;

use Dynamap\Mapping\Exception\TableNameNotSpecifiedException;
use Dynamap\Mapping\Exception\MappingNotSpeficiedException;
use Dynamap\Mapping\TableMapping;
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
        $this->expectException(MappingNotSpeficiedException::class);
        TableMapping::fromArray(
            [
                'name' => 'my_table'
            ]
        );
    }

    public function test a valid mapping(): void
    {
        $mapping = TableMapping::fromArray([
            'name' => 'my_table',
            'mappings' => [
                'Some\\Class\\FQCN' => []
            ]
        ]);

        $this->assertSame('my_table', $mapping->getTableName());
    }
}
