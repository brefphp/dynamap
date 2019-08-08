<?php

namespace Dynamap\Mapping\Field;

interface DynamoDBField
{
    public function getOriginalFieldType(): string;

    public function getDynamoDBFieldType(): string;

    public function castToDynamoDBType($value);
}
