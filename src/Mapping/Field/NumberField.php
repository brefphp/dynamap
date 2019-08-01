<?php declare(strict_types=1);

namespace Dynamap\Mapping\Field;

class NumberField implements DynamoDBField
{
    public function getDynamoDBFieldType(): string
    {
        return 'N';
    }
}
