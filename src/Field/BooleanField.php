<?php declare(strict_types=1);

namespace Dynamap\Field;

class BooleanField extends Field
{
    public function dynamoDbType(): string
    {
        return 'BOOL';
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueForDynamoDbFormat(mixed $value): bool
    {
        return (bool) $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueFromDynamoDbFormat(mixed $value): bool
    {
        return (bool) $value;
    }
}
