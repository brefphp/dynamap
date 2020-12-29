<?php declare(strict_types=1);

namespace Dynamap\Field;

abstract class Field
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function readFieldValue(array $item, string $fieldName): mixed
    {
        $rawDynamoDbValue = $item[$fieldName][$this->dynamoDbType()];

        return $this->castValueFromDynamoDbFormat($rawDynamoDbValue);
    }

    public function dynamoDbQueryValue(mixed $fieldValue): array
    {
        return [
            $this->dynamoDbType() => $this->castValueForDynamoDbFormat($fieldValue),
        ];
    }

    abstract protected function dynamoDbType(): string;

    abstract protected function castValueForDynamoDbFormat(mixed $value): mixed;

    abstract protected function castValueFromDynamoDbFormat(mixed $value): mixed;
}
