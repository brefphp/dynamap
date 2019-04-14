<?php declare(strict_types=1);

namespace Dynamap;

class FieldMapping
{
    /** @var string */
    private $name;

    /** @var string */
    private $dynamoDbType;

    public function __construct(string $name, string $dynamoDbType)
    {
        $this->name = $name;
        $this->dynamoDbType = $dynamoDbType;
    }

    public static function stringField(string $name): self
    {
        return new self($name, 'S');
    }

    public static function intField(string $name): self
    {
        return new self($name, 'N');
    }

    public static function boolField(string $name): self
    {
        return new self($name, 'BOOL');
    }

    public function name(): string
    {
        return $this->name;
    }

    public function readFieldValue(array $item, string $fieldName)
    {
        $rawDynamoDbValue = $item[$fieldName][$this->dynamoDbType()];

        return $this->castValueFromDynamoDbFormat($rawDynamoDbValue);
    }

    public function dynamoDbQueryValue($fieldValue): array
    {
        return [
            $this->dynamoDbType() => $this->castValueForDynamoDbFormat($fieldValue)
        ];
    }

    public function dynamoDbType(): string
    {
        return $this->dynamoDbType;
    }

    private function castValueForDynamoDbFormat($value)
    {
        switch ($this->dynamoDbType()) {
            case 'S':
                return (string) $value;
            case 'N':
                // Numbers should be sent as strings to DynamoDB
                return (string) $value;
            case 'BOOL':
                return (bool) $value;
            default:
                return $value;
        }
    }

    private function castValueFromDynamoDbFormat($value)
    {
        switch ($this->dynamoDbType()) {
            case 'S':
                return (string) $value;
            case 'N':
                return (int) $value;
            case 'BOOL':
                return (bool) $value;
            default:
                return $value;
        }
    }
}
