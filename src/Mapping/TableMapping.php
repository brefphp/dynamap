<?php declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\MappingNotFoundException;
use Dynamap\Mapping\Exception\MappingNotSpecifiedException;
use Dynamap\Mapping\Exception\TableNameNotSpecifiedException;
use Dynamap\Mapping\Field\DynamoDBField;

final class TableMapping
{
    /** @var string */
    private $tableName;

    /** @var ClassMapping[]; */
    private $classMappings = [];

    private function __construct(string $tableName, array $classMapping)
    {
        $this->tableName = $tableName;

        $this->classMappings = $classMapping;
    }

    public static function fromArray(array $config): TableMapping
    {
        if (\array_key_exists('name', $config) === false) {
            throw new TableNameNotSpecifiedException('The table name must be specified');
        }

        if (\array_key_exists('mappings', $config) === false || empty($config['mappings']) === true) {
            throw new MappingNotSpecifiedException('You must provide at least one class mapping for the table ' . $config['name']);
        }

        $classMappings = [];
        foreach ($config['mappings'] as $className => $mapping) {
            $classMappings[] = ClassMapping::fromArray($className, $mapping);
        }

        return new static($config['name'], $classMappings);
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    // todo: add a test for this method
    public function containsMappingForClass(string $className): bool
    {
        foreach ($this->classMappings as $classMapping) {
            if ($className === $classMapping->getClassName()) {
                return true;
            }
        }

        return false;
    }

    public function isClassPropertyMapped(string $className, string $propertyName): bool
    {
        // todo: add a test for this
        if (false === $this->containsMappingForClass($className)) {
            return false;
        }

        foreach ($this->classMappings as $classMapping) {
            if ($classMapping->getClassName() === $className) {
                return $classMapping->hasMappedProperty($propertyName);
            }
        }

        return false;
    }

    // todo: add a test for this
    public function getTypeFor(string $className, string $propertyName): DynamoDBField
    {
        if (false === $this->isClassPropertyMapped($className, $propertyName)) {
            // todo: is this the right exception class?
            throw new MappingNotFoundException('Property ' . $propertyName . ' is not mapped');
        }

        foreach($this->classMappings as $classMapping) {
            if($classMapping->getClassName() === $className) {
                return $classMapping->getMappedProperty($propertyName);
            }
        }
    }
}
