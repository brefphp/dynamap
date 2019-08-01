<?php declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Field\DynamoDBField;
use Dynamap\Mapping\Exception\CannotMapNonExistentFieldException;
use Dynamap\Mapping\Exception\ClassNameInvalidException;
use Dynamap\Mapping\Exception\MappingNotFoundException;
use Dynamap\Mapping\Exception\NoFieldsMappedForClassException;

class ClassMapping
{
    /** @var string */
    private $className;
    /** @var array */
    private $mapping;

    private function __construct(string $className, array $config)
    {
        $this->className = $className;
        $this->mapping = $config;
    }

    public static function fromArray(string $className, array $config): ClassMapping
    {
        if (false === \class_exists($className)) {
            throw new ClassNameInvalidException('Could not map ' . $className . ' as the class was not found');
        }

        if (false === empty($config['fields'])) {
            $fields = self::mapProperties($className, $config['fields']);

            $config['fields'] = $fields;
        }

        return new static($className, $config);
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getMappedProperty(string $propertyName): DynamoDBField
    {
        if (true === empty($this->mapping['fields'])) { // todo: add a test for this
            throw new NoFieldsMappedForClassException('You have tried to access mapping for a class which has no mapped properties');
        }

        if (false === \array_key_exists($propertyName, $this->mapping['fields'])) {
            throw new MappingNotFoundException('Mapping for ' . $propertyName . ' could not be found');
        }

        return $this->mapping['fields'][$propertyName];
    }

    /**
     * @param string $className
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
        $factory = new FieldMappingFactory();

        foreach ($fields as $classField => $type) {
            if (false === \in_array($classField, $classProperties, false)) {
                throw new CannotMapNonExistentFieldException('The field ' . $classField . ' does not exist in ' . $className);
            }

            $mappedFields[$classField] = $factory->getDynamoDbType($type);
        }

        return $mappedFields;
    }
}
