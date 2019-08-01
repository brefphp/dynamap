<?php

namespace Dynamap\Mapping\Field;

interface DynamoDBField
{
    public function getDynamoDBFieldType(): string;
}
