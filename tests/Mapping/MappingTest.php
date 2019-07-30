<?php

namespace Dynamap\Test\Mapping;

use Dynamap\Mapping\Exception\NoTableSpeficiedException;
use Dynamap\Mapping\Mapping;
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
            'tables' => []
        ]);
    }

    public function test a table mapping is created(): void
    {
        $mapping = Mapping::fromConfigArray([
            'tables' => [
                [
                    'name' => 'my_table',
                    'mappings' => [
                        'Some\\Class\\FQCN' => []
                    ]
                ]
            ]
        ]);

        $this->assertSame('my_table', $mapping->getTableFor('Some\\Class\\FQCN'));
    }
}
