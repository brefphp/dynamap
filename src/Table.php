<?php declare(strict_types=1);

namespace Dynamap;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Dynamap\Exception\ItemNotFound;
use Dynamap\Exception\TableNotFound;

class Table
{
    /** @var DynamoDbClient */
    private $dynamoDb;

    /** @var TableMapping */
    private $mapping;

    public function __construct(DynamoDbClient $dynamoDb, TableMapping $mapping)
    {
        $this->dynamoDb = $dynamoDb;
        $this->mapping = $mapping;
    }

    public function getAll(): array
    {
        $items = $this->dynamoDb->scan([
            'TableName' => $this->mapping->getTableName(),
        ])['Items'];

        return $this->mapList($items);
    }

    /**
     * Get item by primary key.
     *
     * Throws an exception if the item cannot be found (see `find()` as an alternative).
     *
     * @param int|string|array $key
     * @throws \InvalidArgumentException If the key is invalid.
     * @throws ItemNotFound If the item cannot be found.
     */
    public function get($key): object
    {
        $item = $this->find($key);
        if ($item === null) {
            throw ItemNotFound::fromKey($this->mapping->getClassName(), $key);
        }

        return $item;
    }

    /**
     * Find an item by primary key.
     *
     * Returns null if the item cannot be found (see `get()` as an alternative).
     *
     * @param int|string|array $key
     * @throws \InvalidArgumentException If the key is invalid.
     */
    public function find($key): ?object
    {
        $table = $this->mapping->getTableName();

        try {
            $item = $this->dynamoDb->getItem([
                'TableName' => $table,
                'Key' => $this->buildKeyQuery($key),
            ])['Item'];
        } catch (DynamoDbException $e) {
            if ($e->getAwsErrorCode() === 'ResourceNotFoundException') {
                throw TableNotFound::tableMissingInDynamoDb($table, $e);
            }
            throw $e;
        }

        if ($item === null) {
            return null;
        }

        return $this->map($item);
    }

    public function save(object $object): void
    {
        $reflectedObject = new \ReflectionObject($object);

        $item = [];
        foreach ($this->mapping->getKeyMapping() as $fieldMapping) {
            $property = $reflectedObject->getProperty($fieldMapping->name());
            $property->setAccessible(true);
            $fieldValue = $property->getValue($object);
            if ($fieldValue === null) {
                throw new \Exception('The object cannot have a null key');
            }
            $item[$fieldMapping->name()] = $fieldMapping->dynamoDbQueryValue($fieldValue);
        }

        foreach ($this->mapping->getFieldsMapping() as $fieldMapping) {
            $property = $reflectedObject->getProperty($fieldMapping->name());
            $property->setAccessible(true);
            $fieldValue = $property->getValue($object);
            // If the value is null we skip sending it (we cannot explicitly send null)
            if ($fieldValue !== null) {
                $item[$fieldMapping->name()] = $fieldMapping->dynamoDbQueryValue($fieldValue);
            }
        }

        $this->dynamoDb->putItem([
            'TableName' => $this->mapping->getTableName(),
            'Item' => $item,
        ]);
    }

    /**
     * Update only specific fields for an item.
     *
     * Warning: if the item has been loaded as a PHP object, the PHP object will not be updated.
     * If you want it to be updated you will need to reload it from database.
     *
     * @param int|string|array $itemKey
     * @param array $values Key-value map
     * @throws \Exception
     */
    public function partialUpdate($itemKey, array $values): void
    {
        $key = $this->buildKeyQuery($itemKey);

        $updateExpressionParts = [];
        $updateValues = [];
        foreach ($values as $fieldName => $value) {
            $fieldMapping = $this->mapping->getFieldMapping($fieldName);
            $updateExpressionParts[] = "$fieldName = :$fieldName";

            $updateValues[':' . $fieldName] = $fieldMapping->dynamoDbQueryValue($value);
        }
        $updateExpression = 'set ' . implode(', ', $updateExpressionParts);

        $this->dynamoDb->updateItem([
            'TableName' => $this->mapping->getTableName(),
            'Key' => $key,
            'UpdateExpression' => $updateExpression,
            'ExpressionAttributeValues' => $updateValues,
        ]);
    }

    private function mapList(array $items): array
    {
        return array_map(function (array $item) {
            return $this->map($item);
        }, $items);
    }

    private function map(array $item): object
    {
        $class = new \ReflectionClass($this->mapping->getClassName());
        $object = $class->newInstanceWithoutConstructor();

        foreach ($item as $fieldName => $fieldData) {
            if (! $this->mapping->hasFieldOrKey($fieldName)) {
                continue;
            }
            $fieldMapping = $this->mapping->getFieldMapping($fieldName);

            $property = $class->getProperty($fieldName);
            $property->setAccessible(true);
            $property->setValue($object, $fieldMapping->readFieldValue($item, $fieldName));
        }

        return $object;
    }

    /**
     * @param int|string|array $key
     * @throws \InvalidArgumentException
     */
    private function buildKeyQuery($key): array
    {
        $keyQuery = [];
        if (! is_array($key)) {
            if ($this->mapping->isCompositeKey()) {
                throw new \InvalidArgumentException('The key is a composite key and only a single value was provided');
            }
            foreach ($this->mapping->getKeyMapping() as $fieldMapping) {
                $keyQuery[$fieldMapping->name()] = $fieldMapping->dynamoDbQueryValue($key);
            }
        } else {
            foreach ($this->mapping->getKeyMapping() as $fieldMapping) {
                $fieldName = $fieldMapping->name();
                if (! isset($key[$fieldName])) {
                    throw new \InvalidArgumentException('The key is incomplete');
                }
                $keyQuery[$fieldName] = $fieldMapping->dynamoDbQueryValue($key[$fieldName]);
            }
        }
        return $keyQuery;
    }
}
