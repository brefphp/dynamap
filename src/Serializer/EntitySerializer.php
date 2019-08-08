<?php

namespace Dynamap\Serializer;

use Dynamap\Mapping\Field\StringField;
use Dynamap\Mapping\Mapping;

class EntitySerializer
{
    /**
     * @var Mapping
     */
    private $mapping;

    public function __construct(Mapping $mapping)
    {
        $this->mapping = $mapping;
    }

    public function serialize(object $entity): array
    {
        $properties = [];
        $className = \get_class($entity);

        $reflection = new \ReflectionObject($entity);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            if (true === $this->mapping->isClassPropertyMapped($className, $property->getName())) {
                $properties[$reflection->getShortName() . '_' . $property->getName()] = $this->transform($entity, $property);
            }
        }

        return $properties;
    }

    private function transform($entity, $property)
    {
        $type = $this->mapping->getTypeFor(\get_class($entity), $property->getName());

        return $type->castToDynamoDBType($property->getValue($entity));

        var_dump($type);
//        var_dump((string)$property->getValue($entity));
        return $property->getValue($entity);
    }
}