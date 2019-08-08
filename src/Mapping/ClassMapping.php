<?php declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\CannotMapNonExistentFieldException;
use Dynamap\Mapping\Exception\ClassNameInvalidException;
use Dynamap\Mapping\Exception\MappingNotFoundException;
use Dynamap\Mapping\Exception\NoFieldsMappedForClassException;
use Dynamap\Mapping\Field\DynamoDBField;

class ClassMapping
{
    /** @var string */
    private $tableName;
    /** @var string */
    private $className;
    /** @var array */
    private $mapping;

    private function __construct(string $tableName, string $className, array $config)
    {
        $this->className = $className;
        $this->mapping = $config;
        $this->tableName = $tableName;
    }

    public static function fromArray(string $tableName, string $className, array $config): ClassMapping
    {
        if (\class_exists($className) === false) {
            throw new ClassNameInvalidException('Could not map ' . $className . ' as the class was not found');
        }

        if (empty($config['fields']) === false) {
            $fields = self::mapProperties($className, $config['fields']);

            $config['fields'] = $fields;
        }

        return new static($tableName, $className, $config);
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getMappedProperty(string $propertyName): DynamoDBField
    {
        if (empty($this->mapping['fields']) === true) { // todo: add a test for this
            throw new NoFieldsMappedForClassException('You have tried to access mapping for a class which has no mapped properties');
        }

        if ($this->hasMappedProperty($propertyName) === false) {
            throw new MappingNotFoundException('Mapping for ' . $propertyName . ' could not be found');
        }

        return $this->mapping['fields'][$propertyName];
    }

    public function hasMappedProperty(string $propertyName): bool
    {
        // todo: add a test for this
        if (\array_key_exists('fields', $this->mapping) === false) {
            return false;
        }

        return \array_key_exists($propertyName, $this->mapping['fields']);
    }

    /**
     * @param array $fields
     * @return array
     * @throws CannotMapNonExistentFieldException
     * @throws \ReflectionException
     */
    private static function mapProperties(string $className, array $fields): array
    {
        $reflection = new \ReflectionClass($className);

        $classProperties = array_reduce($reflection->getProperties(), static function ($carry, $item) {
            $carry[] = $item->getName();
            return $carry;
        }, []);

        $mappedFields = [];
        $factory = new FieldMappingFactory;

        foreach ($fields as $classField => $type) {
            if (\in_array($classField, $classProperties, false) === false) {
                throw new CannotMapNonExistentFieldException('The field ' . $classField . ' does not exist in ' . $className);
            }

            $mappedFields[$classField] = $factory->getDynamoDbType($type);
        }

        return $mappedFields;
    }
}
