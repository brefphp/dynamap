<?php declare(strict_types=1);

namespace Dynamap\Mapping\Field;

class DateTimeField implements DynamoDBField
{
    public function getDynamoDBFieldType(): string
    {
        return 'S';
    }
}
