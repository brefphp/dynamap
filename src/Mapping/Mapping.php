<?php declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\ClassNameInvalidException;
use Dynamap\Mapping\Exception\MappingNotFoundException;
use Dynamap\Mapping\Exception\NoTableSpeficiedException;

final class Mapping
{
    /** @var ClassMapping[] */
    private $mapping = [];

    private function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public static function fromConfigArray(array $config): self
    {
        if (\array_key_exists('tables', $config) === false || empty($config['tables'])) {
            throw new NoTableSpeficiedException('Dynamap needs at least one table to work with!');
        }

        $mapping = \array_reduce($config['tables'], static function ($carry, $item) {
            foreach ($item['mappings'] as $classname => $properties) {
                $carry[] = ClassMapping::fromArray($item['name'], $classname, $properties);
            }
            return $carry;
        }, []);

        return new static($mapping);
    }

    public function getTableFor(string $className): string
    {
        // todo: add a test for this
        if (false === \class_exists($className)) {
            throw new ClassNameInvalidException('Could not get table for ' . $className . ' as the class was not found');
        }

        foreach ($this->mapping as $mapping) {
            if ($mapping->getClassName() === $className) {
                return $mapping->getTableName();
            }
        }

        // todo: add a test for this
        throw new ClassNotMappedException('The class ' . $className . ' was not found in the mapping configuration');
    }

    public function isClassPropertyMapped(string $className, string $propertyName): bool
    {
        // todo: add a test for this
        if (false === \class_exists($className)) {
            return false;
        }

        foreach ($this->mapping as $mapping) {
            if ($mapping->getClassName() === $className) {
                return $mapping->hasMappedProperty($propertyName);
            }
        }

        return false;
    }

    // todo: add a test for this
    public function getTypeFor(string $className, string $propertyName)
    {
        if (false === $this->isClassPropertyMapped($className, $propertyName)) {
            throw new MappingNotFoundException('Mapping for ' . $propertyName . ' could not be found');
        }

        foreach ($this->mapping as $mapping) {
            if ($mapping->getClassName() === $className) {

                if (false === $mapping->hasMappedProperty($propertyName)) {
                    // todo: is this the right exception class? add a test for this
                    throw new MappingNotFoundException('Property ' . $propertyName . ' is not mapped');
                }

                return $mapping->getMappedProperty($propertyName);
            }
        }
    }
}
