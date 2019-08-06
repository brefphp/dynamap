<?php

namespace Dynamap\Mapping;

use Dynamap\Mapping\Field\BooleanField;
use Dynamap\Mapping\Field\DynamoDBField;
use Dynamap\Mapping\Field\NumberField;
use Dynamap\Mapping\Field\StringField;

class FieldMappingFactory
{
    public function getDynamoDBType(string $propertyType): DynamoDBField
    {
        switch (strtolower($propertyType)) {
            case 'uuid' :
            case 'string':
            case 'datetime':
                return new StringField();
                break;
            case 'float':
            case 'integer':
                return new NumberField();
                break;
            case 'boolean':
                return new BooleanField();
                break;
            default: // todo: add a test for this
                throw new InvalidMappingTypeException();
        }
    }
}
