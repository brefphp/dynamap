<?php declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\CannotMapNonExistentFieldException;
use Dynamap\Mapping\Exception\ClassNameInvalidException;

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

    /**
     * @param array $fields
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
        foreach ($fields as $classField => $type) {
            if (\in_array($classField, $classProperties) === false) {
                throw new CannotMapNonExistentFieldException('The field ' . $classField . ' does not exist in ' . $className);
            }

            $mappedFields[$classField] = $factory->getType($type);
        }



        return $fields;
    }
}
