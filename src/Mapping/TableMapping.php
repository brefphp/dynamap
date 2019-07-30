<?php
declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\MappingNotSpeficiedException;
use Dynamap\Mapping\Exception\TableNameNotSpecifiedException;

final class TableMapping
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var ClassMapping[];
     */
    private $classMappings = [];

    private function __construct(string $tableName, ClassMapping $classMapping)
    {
        $this->tableName = $tableName;

        $this->classMappings[] = $classMapping;
    }

    public static function fromArray(array $config): TableMapping
    {
        if (false === \array_key_exists('name', $config)) {
            throw new TableNameNotSpecifiedException('The table name must be specified');
        }

        if (false === \array_key_exists('mappings', $config) || true === empty($config['mappings'])) {
            throw new MappingNotSpeficiedException('You must provide at least one class mapping for the table ' . $config['name']);
        }

        $classMappings = array_reduce($config['mappings'], static function ($carry, $item) {
            // todo: add class mappings
        }, []);

        return new static($config['name'], $classMappings);
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }
}
