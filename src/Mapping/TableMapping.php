<?php declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\MappingNotSpecifiedException;
use Dynamap\Mapping\Exception\TableNameNotSpecifiedException;

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
}
