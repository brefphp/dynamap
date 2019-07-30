<?php

namespace Dynamap\Test\TestCase;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Dynamap\Dynamap;
use Dynamap\Test\Fixture\Article;
use PHPUnit\Framework\TestCase;

class DynamapTestCase extends TestCase
{
    /** @var Dynamap */
    protected $dynamap;

    public function setUp(): void
    {
        $dynamoDb = new DynamoDbClient([
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
            $dynamoDb->deleteTable([
                'TableName' => 'articles',
            ]);
        } catch (DynamoDbException $e) {
            // The table doesn't exist the first time
        }

        $dynamoDb->createTable([
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

        $mapping = [
            Article::class => [
                'table' => 'articles',
                'keys' => [
                    'id',
                ],
            ],
            'UnknownClass' => [ // This is a class that doesn't exist
                'table' => 'articles',
                'keys' => [
                    'id',
                ],
            ],
            \stdClass::class => [
                'table' => 'foo', // This is a table that doesn't exist
                'keys' => [
                    'id',
                ],
            ],
        ];
        $this->dynamap = new Dynamap($dynamoDb, $mapping);
    }
}
