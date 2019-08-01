<?php declare(strict_types=1);

namespace Dynamap\Field;

abstract class Field
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function readFieldValue(array $item, string $fieldName)
    {
        $rawDynamoDbValue = $item[$fieldName][$this->getDynamoDbType()];

        return $this->castValueFromDynamoDbFormat($rawDynamoDbValue);
    }

    /**
     * @param mixed $fieldValue
     */
    public function dynamoDbQueryValue($fieldValue): array
    {
        return [
            $this->getDynamoDbType() => $this->castValueForDynamoDbFormat($fieldValue),
        ];
    }

    abstract protected function getDynamoDbType(): string;

    /**
     * @param mixed $value
     * @return mixed
     */
    abstract protected function castValueForDynamoDbFormat($value);

    /**
     * @param mixed $value
     * @return mixed
     */
    abstract protected function castValueFromDynamoDbFormat($value);
}
