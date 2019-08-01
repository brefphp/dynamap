<?php declare(strict_types=1);

namespace Dynamap\Field;

class BooleanField extends Field
{
    public function getDynamoDbType(): string
    {
        return 'BOOL';
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueForDynamoDbFormat($value): bool
    {
        return (bool) $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueFromDynamoDbFormat($value): bool
    {
        return (bool) $value;
    }
}
