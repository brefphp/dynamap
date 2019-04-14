<?php declare(strict_types=1);

namespace Dynamap\Field;

class StringField extends Field
{
    public function dynamoDbType(): string
    {
        return 'S';
    }

    protected function castValueForDynamoDbFormat($value): string
    {
        return (string) $value;
    }

    protected function castValueFromDynamoDbFormat($value): string
    {
        return (string) $value;
    }
}
