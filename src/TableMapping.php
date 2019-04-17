<?php declare(strict_types=1);

namespace Dynamap;

use Dynamap\Field\BooleanField;
use Dynamap\Field\DateTimeField;
use Dynamap\Field\Field;
use Dynamap\Field\FloatField;
use Dynamap\Field\IntegerField;
use Dynamap\Field\StringField;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

class TableMapping
{
    /** @var PropertyInfoExtractorInterface */
    private $propertyInfo;
    /** @var string */
    private $tableName;
    /** @var string */
    private $className;
    /** @var Field[] */
    private $keys = [];
    /** @var Field[] */
    private $fields = [];

    public function __construct(PropertyInfoExtractorInterface $propertyInfo, string $tableName, array $mappingConfig)
    {
        $this->propertyInfo = $propertyInfo;
        $this->tableName = $tableName;
        $this->className = (string) $mappingConfig['class'];

        $this->readProperties($this->className, $mappingConfig['keys']);
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return Field[]
     */
    public function getKeyMapping(): array
    {
        return $this->keys;
    }

    public function isCompositeKey(): bool
    {
        return count($this->keys) > 1;
    }

    /**
     * @return Field[]
     */
    public function getFieldsMapping(): array
    {
        return $this->fields;
    }

    public function getFieldMapping(string $fieldName): Field
    {
        return $this->fields[$fieldName] ?? $this->keys[$fieldName];
    }

    public function hasFieldOrKey(string $fieldName): bool
    {
        return isset($this->fields[$fieldName])
            || isset($this->keys[$fieldName]);
    }

    private function readProperties(string $className, array $keys): void
    {
        $class = new \ReflectionClass($className);
        foreach ($class->getProperties() as $propertyReflection) {
            if ($propertyReflection->isStatic()) {
                continue;
            }
            $property = $propertyReflection->getName();

            $propertyTypes = $this->propertyInfo->getTypes($className, $property);
            if (empty($propertyTypes)) {
                throw new \Exception("Type not recognized for {$className}::\${$property}");
            }
            if (count($propertyTypes) > 1) {
                throw new \Exception("Too many types for {$className}::\${$property}");
            }

            $propertyType = $propertyTypes[0];
            switch ($propertyType->getBuiltinType()) {
                case 'string':
                    $fieldMapping = new StringField($property);
                    break;
                case 'int':
                    $fieldMapping = new IntegerField($property);
                    break;
                case 'float':
                    $fieldMapping = new FloatField($property);
                    break;
                case 'bool':
                    $fieldMapping = new BooleanField($property);
                    break;
                case 'object':
                    switch ($propertyType->getClassName()) {
                        case \DateTime::class:
                        case \DateTimeImmutable::class:
                        case \DateTimeInterface::class:
                            $fieldMapping = new DateTimeField($property);
                            break;
                        default:
                            throw new \Exception("Unsupported type {$propertyType->getClassName()} for {$className}::\${$property}");
                    }
                    break;
                default:
                    throw new \Exception("Unsupported type {$propertyType->getBuiltinType()} for {$className}::\${$property}");
            }

            if (in_array($property, $keys, true)) {
                $this->keys[$property] = $fieldMapping;
            } else {
                $this->fields[$property] = $fieldMapping;
            }
        }
    }
}
