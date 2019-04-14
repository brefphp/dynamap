<?php declare(strict_types=1);

namespace Dynamap\Field;

class IntegerField extends Field
{
    public function dynamoDbType(): string
    {
        return 'N';
    }

    protected function castValueForDynamoDbFormat($value): string
    {
        // Numbers should be sent as strings to DynamoDB
        return (string) $value;
    }

    protected function castValueFromDynamoDbFormat($value): int
    {
        return (int) $value;
    }
}
