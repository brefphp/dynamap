<?php declare(strict_types=1);

namespace Dynamap\Mapping\Field;

class BooleanField implements DynamoDBField
{
    public function getDynamoDBFieldType(): string
    {
        return 'BOOL';
    }
}
