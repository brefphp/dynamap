<?php declare(strict_types=1);

namespace Dynamap;

use Aws\DynamoDb\DynamoDbClient;

class Dynamap
{
    /** @var DynamoDbClient */
    private $dynamoDb;

    /** @var Mapping */
    private $mappingObj;

    public function __construct(DynamoDbClient $dynamoDb, array $mapping)
    {
        $this->dynamoDb = $dynamoDb;
        $this->mappingObj = new Mapping($mapping);
    }

    public static function fromOptions(array $options, array $mapping)
    {
        $options['version'] = $options['version'] ?? 'latest';

        return new static(new DynamoDbClient($options), $mapping);
    }

    public function getAll(string $table): array
    {
        $items = $this->dynamoDb->scan([
            'TableName' => $table,
        ])['Items'];

        return $this->mapList($items, $table);
    }

    /**
     * Get item by primary key.
     */
    public function get(string $table, $key)
    {
        $tableMapping = $this->mappingObj->getTableMapping($table);

        $item = $this->dynamoDb->getItem([
            'TableName' => $table,
            'Key' => $this->buildKeyQuery($key, $tableMapping),
        ])['Item'];

        return $this->map($item, $table);
    }

    public function save(object $object): void
    {
        $reflectedObject = new \ReflectionObject($object);
        $tableMapping = $this->mappingObj->getTableFromClassName($reflectedObject->getName());

        $item = [];
        foreach ($tableMapping->getKeyMapping() as $fieldMapping) {
            $property = $reflectedObject->getProperty($fieldMapping->name());
            $property->setAccessible(true);
            $fieldValue = $property->getValue($object);
            if ($fieldValue === null) {
                throw new \Exception('The object cannot have a null key');
            }
            $item[$fieldMapping->name()] = $fieldMapping->dynamoDbQueryValue($fieldValue);
        }

        foreach ($tableMapping->getFieldsMapping() as $fieldMapping) {
            $property = $reflectedObject->getProperty($fieldMapping->name());
            $property->setAccessible(true);
            $fieldValue = $property->getValue($object);
            // If the value is null we skip sending it (we cannot explicitly send null)
            if ($fieldValue !== null) {
                $item[$fieldMapping->name()] = $fieldMapping->dynamoDbQueryValue($fieldValue);
            }
        }

        $this->dynamoDb->putItem([
            'TableName' => $tableMapping->getTableName(),
            'Item' => $item,
        ]);
    }

    public function partialUpdate(string $table, $itemKey, array $values): void
    {
        $tableMapping = $this->mappingObj->getTableMapping($table);

        $key = $this->buildKeyQuery($itemKey, $tableMapping);

        $updateExpressionParts = [];
        $updateValues = [];
        foreach ($values as $fieldName => $value) {
            $fieldMapping = $tableMapping->getFieldMapping($fieldName);
            $updateExpressionParts[] = "$fieldName = :$fieldName";

            $updateValues[':' . $fieldName] = $fieldMapping->dynamoDbQueryValue($value);
        }
        $updateExpression = 'set ' . implode(', ', $updateExpressionParts);

        $this->dynamoDb->updateItem([
            'TableName' => $table,
            'Key' => $key,
            'UpdateExpression' => $updateExpression,
            'ExpressionAttributeValues' => $updateValues,
        ]);
    }

    private function mapList(array $items, string $table): array
    {
        return array_map(function (array $item) use ($table) {
            return $this->map($item, $table);
        }, $items);
    }

    private function map(array $item, string $table): object
    {
        $tableMapping = $this->mappingObj->getTableMapping($table);

        $class = new \ReflectionClass($tableMapping->getClassName());
        $object = $class->newInstanceWithoutConstructor();

        foreach ($item as $fieldName => $fieldData) {
            if (!$tableMapping->hasFieldOrKey($fieldName)) {
                continue;
            }
            $fieldMapping = $tableMapping->getFieldMapping($fieldName);

            $property = $class->getProperty($fieldName);
            $property->setAccessible(true);
            $property->setValue($object, $fieldMapping->readFieldValue($item, $fieldName));
        }

        return $object;
    }

    private function buildKeyQuery($key, TableMapping $tableMapping): array
    {
        $keyQuery = [];
        if (! is_array($key)) {
            if ($tableMapping->isCompositeKey()) {
                throw new \Exception('The key is a composite key and only a single value was provided');
            }
            foreach ($tableMapping->getKeyMapping() as $fieldMapping) {
                $keyQuery[$fieldMapping->name()] = $fieldMapping->dynamoDbQueryValue($key);
            }
        } else {
            foreach ($tableMapping->getKeyMapping() as $fieldMapping) {
                $fieldName = $fieldMapping->name();
                if (! isset($key[$fieldName])) {
                    throw new \Exception('The key is incomplete');
                }
                $keyQuery[$fieldName] = $fieldMapping->dynamoDbQueryValue($key[$fieldName]);
            }
        }
        return $keyQuery;
    }
}