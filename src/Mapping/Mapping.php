<?php declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\ClassNameInvalidException;
use Dynamap\Mapping\Exception\NoTableSpeficiedException;

final class Mapping
{
    /** @var array */
    private $mapping = [];

    private function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public static function fromConfigArray(array $config)
    {
        if (\array_key_exists('tables', $config) === false || empty($config['tables'])) {
            throw new NoTableSpeficiedException('Dynamap needs at least one table to work with!');
        }

        $mapping = \array_reduce($config['tables'], static function ($carry, $item) {
            $carry[] = TableMapping::fromArray($item);
            return $carry;
        }, []);

        return new static($mapping);
    }

    public function getTableFor(string $className): string
    {
        // todo: add a test for this
        if (false === \class_exists($className)) {
            throw new ClassNameInvalidException('Get table for ' . $className . ' as the class was not found');
        }

        foreach ($this->mapping as $mappedTable) {
            if ($mappedTable->containsMappingForClass($className)) {
                return $mappedTable->getTableName();
            }
        }

        // todo: add a test for this
        throw new ClassNotMappedException('The class ' . $className . ' was not found in the mapping configuration');
    }
}
