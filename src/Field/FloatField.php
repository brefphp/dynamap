<?php declare(strict_types=1);

namespace Dynamap\Field;

class FloatField extends Field
{
    public function dynamoDbType(): string
    {
        return 'N';
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueForDynamoDbFormat($value): string
    {
        // Numbers should be sent as strings to DynamoDB
        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueFromDynamoDbFormat($value): float
    {
        return (float) $value;
    }
}
