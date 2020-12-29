<?php declare(strict_types=1);

namespace Dynamap\Field;

use DateTimeInterface;

class DateTimeField extends Field
{
    public function dynamoDbType(): string
    {
        // Dates are stored as strings
        return 'S';
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueForDynamoDbFormat(mixed $value): string
    {
        if (! $value instanceof DateTimeInterface) {
            throw new \InvalidArgumentException('Expected an instance of DateTimeInterface');
        }

        return $value->format(DateTimeInterface::ATOM);
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueFromDynamoDbFormat(mixed $value): DateTimeInterface
    {
        return \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $value);
    }
}
