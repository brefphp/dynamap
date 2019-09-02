<?php declare(strict_types=1);

namespace Dynamap\Serializer;

use Dynamap\Mapping\Mapping;

class EntitySerializer
{
    /** @var Mapping */
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
            if (null === $property->getValue($entity)) {
                # todo: add a test for this
                continue;
            }
            if ($this->mapping->isClassPropertyMapped($className, $property->getName()) === true) {
                $properties[$reflection->getName() . '_' . $property->getName()] = $this->transform($entity, $property);
            } else {
                $properties[$reflection->getName() . '_' . $property->getName()] = $property->getValue($entity);
            }
        }

        return $properties;
    }

    public function unserialize(array $serialized): object
    {

        $objects = [];

        foreach ($serialized as $key => $value) {

            $prefixLength = \strpos($key, '_');
            $className = \substr($key, 0, $prefixLength);
            $propertyName = \substr($key, $prefixLength + 1);

            if (false === \in_array($className, $objects)) {
                $objects[] = $className;
                $concretion = (new \ReflectionClass($className))->newInstance();
                $reflection = new \ReflectionClass($className);
            }

            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);

            if ($this->mapping->isClassPropertyMapped($className, $propertyName) === true) {
                $value = $this->untransform($concretion, $propertyName, $value);
            }

            $property->setValue($concretion, $value);
        }

        return $concretion;
    }

    private function transform($entity, $property)
    {
        $type = $this->mapping->getTypeFor(\get_class($entity), $property->getName());

        return $type->castToDynamoDBType($property->getValue($entity));
    }

    private function untransform($entity, $property, $value) {
        $type = $this->mapping->getTypeFor(\get_class($entity), $property);

        return $type->restoreFromDynamoDBType($value);
    }
}
