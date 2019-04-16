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
    protected function castValueForDynamoDbFormat($value): string
    {
        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueFromDynamoDbFormat($value): string
    {
        return (string) $value;
    }
}
