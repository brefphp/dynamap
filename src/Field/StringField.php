<?php declare(strict_types=1);

namespace Dynamap\Field;

class StringField extends Field
{
    public function dynamoDbType(): string
    {
        return 'S';
    }

    protected function castValueForDynamoDbFormat(mixed $value): string
    {
        return (string) $value;
    }

    protected function castValueFromDynamoDbFormat(mixed $value): string
    {
        return (string) $value;
    }
}
