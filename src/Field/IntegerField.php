<?php declare(strict_types=1);

namespace Dynamap\Field;

class IntegerField extends Field
{
    public function dynamoDbType(): string
    {
        return 'N';
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueForDynamoDbFormat(mixed $value): string
    {
        // Numbers should be sent as strings to DynamoDB
        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueFromDynamoDbFormat(mixed $value): int
    {
        return (int) $value;
    }
}
