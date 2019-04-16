<?php declare(strict_types=1);

namespace Dynamap\Field;

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
    protected function castValueForDynamoDbFormat($value): string
    {
        if (! $value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException('Expected an instance of DateTimeInterface');
        }

        return $value->format(\DateTimeInterface::ATOM);
    }

    /**
     * {@inheritdoc}
     */
    protected function castValueFromDynamoDbFormat($value): \DateTimeInterface
    {
        return \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $value);
    }
}
