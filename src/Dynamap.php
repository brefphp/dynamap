<?php declare(strict_types=1);

namespace Dynamap;

use Aws\DynamoDb\DynamoDbClient;
use Dynamap\Exception\ItemNotFound;
use Exception;
use InvalidArgumentException;

class Dynamap
{
    private DynamoDbClient $dynamoDb;
    private Mapping $mapping;
    /** @var Table[] */
    private array $tables = [];

    public function __construct(DynamoDbClient $dynamoDb, array $mapping)
    {
        $this->dynamoDb = $dynamoDb;
        $this->mapping = new Mapping($mapping);
    }

    public static function fromOptions(array $options, array $mapping): self
    {
        $options['version'] = $options['version'] ?? 'latest';

        return new static(new DynamoDbClient($options), $mapping);
    }

    public function getTable(string $className): Table
    {
        if (! isset($this->tables[$className])) {
            $tableMapping = $this->mapping->getTableMapping($className);
            $this->tables[$className] = new Table($this->dynamoDb, $tableMapping);
        }

        return $this->tables[$className];
    }

    public function getAll(string $class): array
    {
        return $this->getTable($class)->getAll();
    }

    /**
     * Get item by primary key.
     *
     * Throws an exception if the item cannot be found (see `find()` as an alternative).
     *
     * @throws InvalidArgumentException If the key is invalid.
     * @throws ItemNotFound If the item cannot be found.
     */
    public function get(string $class, array|int|string $key): object
    {
        return $this->getTable($class)->get($key);
    }

    /**
     * Find an item by primary key.
     *
     * Returns null if the item cannot be found (see `get()` as an alternative).
     *
     * @throws InvalidArgumentException If the key is invalid.
     */
    public function find(string $class, array|int|string $key): ?object
    {
        return $this->getTable($class)->find($key);
    }

    public function save(object $object): void
    {
        $this->getTable(get_class($object))->save($object);
    }

    /**
     * Update only specific fields for an item.
     *
     * Warning: if the item has been loaded as a PHP object, the PHP object will not be updated.
     * If you want it to be updated you will need to reload it from database.
     *
     * @param array $values Key-value map
     * @throws Exception
     */
    public function partialUpdate(string $class, array|int|string $itemKey, array $values): void
    {
        $this->getTable($class)->partialUpdate($itemKey, $values);
    }
}
