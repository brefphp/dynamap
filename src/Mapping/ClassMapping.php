<?php declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\CannotMapNonExistentFieldException;
use Dynamap\Mapping\Exception\ClassNameInvalidException;

class ClassMapping
{
    /** @var string */
    private $className;
    /** @var array */
    private $config;

    private function __construct(string $className, array $config)
    {
        $this->className = $className;
        $this->config = $config;
    }

    public static function fromArray(string $className, array $config): ClassMapping
    {
        if (\class_exists($className) === false) {
            throw new ClassNameInvalidException('Could not map ' . $className . ' as the class was not found');
        }

        if (empty($config['fields']) === false) {
            self::validateMappedProperties($className, $config['fields']);
        }

        return new static($className, $config);
    }

    /**
     * @param array $fields
     * @throws CannotMapNonExistentFieldException
     * @throws \ReflectionException
     */
    private static function validateMappedProperties(string $className, array $fields): void
    {
        $reflection = new \ReflectionClass($className);

        $classProperties = array_reduce($reflection->getProperties(), static function ($carry, $item) {
            $carry[] = $item->getName();
            return $carry;
        }, []);

        foreach ($fields as $mappedField => $type) {
            if (\in_array($mappedField, $classProperties) === false) {
                throw new CannotMapNonExistentFieldException('The field ' . $mappedField . ' does not exist in ' . $className);
            }
        }
    }
}
