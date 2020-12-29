<?php declare(strict_types=1);

namespace Dynamap\Field;

class StringField extends Field
{
    public function dynamoDbType(): string
    {
        return 'S';
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueForDynamoDbFormat(mixed $value): string
    {
        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueFromDynamoDbFormat(mixed $value): string
    {
        return (string) $value;
    }
}
