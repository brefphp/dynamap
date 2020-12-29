<?php declare(strict_types=1);

namespace Dynamap\Test;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use PHPUnit\Framework\TestCase;

abstract class FunctionalTest extends TestCase
{
    protected DynamoDbClient $dynamoDb;

    public function setUp(): void
    {
        $this->dynamoDb = new DynamoDbClient([
            'version' => 'latest',
            'endpoint' => 'http://localhost:8000/',
            // DynamoDB local requires those parameters, even with random values
            'region' => 'us-east-1',
            'credentials' => [
                'key' => 'FAKE_KEY',
                'secret' => 'FAKE_SECRET',
            ],
        ]);

        try {
            $this->dynamoDb->deleteTable([
                'TableName' => 'articles',
            ]);
        } catch (DynamoDbException) {
            // The table doesn't exist the first time
        }

        $this->dynamoDb->createTable([
            'TableName' => 'articles',
            'KeySchema' => [
                [
                    'AttributeName' => 'id',
                    'KeyType' => 'HASH',
                ],
            ],
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'id',
                    'AttributeType' => 'N',
                ],
            ],
            'ProvisionedThroughput' => [
                'WriteCapacityUnits' => 5,
                'ReadCapacityUnits' => 5,
            ],
        ]);
    }
}
