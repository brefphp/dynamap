<?php

namespace Dynamap\Mapping\Field;

class DateTimeField implements DynamoDBField
{
    /**
     * @var string
     */
    private $originalFieldType;

    public function __construct(string $originalFieldType)
    {
        $this->originalFieldType = $originalFieldType;
    }

    public function getDynamoDBFieldType(): string
    {
        return 'S';
    }

    public function getOriginalFieldType(): string
    {
        return $this->originalFieldType;
    }

    public function castToDynamoDBType($value)
    {
        return $value->format(\DateTime::ATOM);
    }
}