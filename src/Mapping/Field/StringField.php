<?php declare(strict_types=1);

namespace Dynamap\Mapping\Field;

use Ramsey\Uuid\Uuid;

class StringField implements DynamoDBField
{
    /** @var string */
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

    public function castToDynamoDBType($value): string
    {
        return (string)$value;
    }

    public function restoreFromDynamoDBType($value)
    {
        if ('uuid' === $this->originalFieldType) {
            return Uuid::fromString($value);
        }

        return (string)$value;
    }
}
