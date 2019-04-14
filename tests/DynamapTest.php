<?php declare(strict_types=1);

namespace Dynamap\Test;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Dynamap\Dynamap;
use Dynamap\Test\Fixture\Article;
use PHPUnit\Framework\TestCase;

class DynamapTest extends TestCase
{
    /** @var Dynamap */
    private $dynamap;

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
            'articles' => [
                'class' => Article::class,
                'keys' => [
                    'id',
                ],
            ],
        ];
        $this->dynamap = new Dynamap($dynamoDb, $mapping);
    }

    public function test store and retrieve object(): void
    {
        $this->dynamap->save(new Article(123));

        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);

        $this->assertEquals(123, $article->getId());
    }

    public function test get empty table(): void
    {
        $this->assertEmpty($this->dynamap->getAll('articles'));
    }

    public function test get all(): void
    {
        $this->dynamap->save(new Article(123));
        $this->dynamap->save(new Article(456));
        $this->dynamap->save(new Article(789));

        $this->assertCount(3, $this->dynamap->getAll('articles'));
    }

    public function test string attribute(): void
    {
        $article = new Article(123);
        $article->setName('Hello world!');
        $this->dynamap->save($article);

        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);

        $this->assertEquals('Hello world!', $article->getName());
    }

    public function test update existing object(): void
    {
        $article = new Article(123);
        $article->setName('Hello world!');
        $this->dynamap->save($article);

        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);

        $article->setName('Hello John!');
        $this->dynamap->save($article);

        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);

        $this->assertEquals('Hello John!', $article->getName());
    }

    public function test boolean attribute(): void
    {
        $article = new Article(123);
        $this->dynamap->save($article);

        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);
        $this->assertFalse($article->isPublished());

        $article->publish();
        $this->dynamap->save($article);

        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);
        $this->assertTrue($article->isPublished());
    }

    public function test date attribute(): void
    {
        $this->dynamap->save(new Article(123));

        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);

        $article->setName('Hello John!');

        $this->assertEqualsWithDelta(new \DateTimeImmutable, $article->getCreationDate(), 1);
    }
}
