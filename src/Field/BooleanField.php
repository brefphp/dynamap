<?php declare(strict_types=1);

namespace Dynamap\Field;

class BooleanField extends Field
{
    public function dynamoDbType(): string
    {
        return 'BOOL';
    }

    protected function castValueForDynamoDbFormat($value): bool
    {
        return (bool) $value;
    }

    protected function castValueFromDynamoDbFormat($value): bool
    {
        return (bool) $value;
    }
}
