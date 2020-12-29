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
    private PropertyInfoExtractorInterface $propertyInfo;
    private string $tableName;
    private string $className;
    /** @var Field[] */
    private array $keys = [];
    /** @var Field[] */
    private array $fields = [];

    public function __construct(PropertyInfoExtractorInterface $propertyInfo, string $className, array $mappingConfig)
    {
        $this->propertyInfo = $propertyInfo;
        $this->className = $className;
        $this->tableName = (string) $mappingConfig['table'];

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
        if (! class_exists($className)) {
            throw new \Exception("The class `$className` doesn't exist");
        }

        $class = new \ReflectionClass($className);
        foreach ($class->getProperties() as $propertyReflection) {
            if ($propertyReflection->isStatic()) {
                continue;
            }
            $property = $propertyReflection->getName();
            $propertyType = $propertyReflection->getType();

            if (!$propertyType) {
                $propertyTypes = $this->propertyInfo->getTypes($className, $property);
                if (empty($propertyTypes)) {
                    throw new \Exception("Type not recognized for {$className}::\${$property}");
                }
                if (count($propertyTypes) > 1) {
                    throw new \Exception("Too many types for {$className}::\${$property}");
                }
                $builtinType = $propertyTypes[0]->getBuiltinType();
                if ($builtinType === 'object') {
                    $propertyTypeClassName = $propertyTypes[0]->getClassName();
                } else {
                    $propertyTypeClassName = null;
                }
            } else {
                $builtinType = $propertyType->isBuiltin() ? $propertyType->getName() : 'object';
                $propertyTypeClassName = $propertyType->getName();
            }

            switch ($builtinType) {
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
                    switch ($propertyTypeClassName) {
                        case \DateTime::class:
                        case \DateTimeImmutable::class:
                        case \DateTimeInterface::class:
                            $fieldMapping = new DateTimeField($property);
                            break;
                        default:
                            throw new \Exception("Unsupported type {$propertyTypeClassName} for {$className}::\${$property}");
                    }
                    break;
                default:
                    throw new \Exception("Unsupported type {$builtinType} for {$className}::\${$property}");
            }

            if (in_array($property, $keys, true)) {
                $this->keys[$property] = $fieldMapping;
            } else {
                $this->fields[$property] = $fieldMapping;
            }
        }
    }
}
