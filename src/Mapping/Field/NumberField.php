<?php declare(strict_types=1);

namespace Dynamap\Mapping\Field;

class NumberField implements DynamoDBField
{
    /** @var string */
    private $originalFieldType;

    public function __construct(string $originalFieldType)
    {
        $this->originalFieldType = $originalFieldType;
    }

    public function getDynamoDBFieldType(): string
    {
        return 'N';
    }

    public function getOriginalFieldType(): string
    {
        return $this->originalFieldType;
    }

    public function castToDynamoDBType($value)
    {
        return $value;
    }

    public function restoreFromDynamoDBType($value)
    {
        return $value;
    }
}
