<?php declare(strict_types=1);

namespace Dynamap\Test;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Dynamap\Dynamap;
use Dynamap\Exception\ItemNotFound;
use Dynamap\Exception\TableNotFound;
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
            'foo' => [ // This is a table that doesn't exist
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

    public function test get unknown object(): void
    {
        $this->expectException(ItemNotFound::class);
        $this->expectExceptionMessage('Item `Dynamap\Test\Fixture\Article` not found for key 123');

        $this->dynamap->get('articles', 123);
    }

    public function test get table that is mapped but doesnt exist in DynamoDB(): void
    {
        $this->expectException(TableNotFound::class);
        $this->expectExceptionMessage('Cannot find the table `foo` in DynamoDB: make sure it exists and that the code has permissions to access it');

        $this->dynamap->get('foo', 123);
    }

    public function test get unknown table(): void
    {
        $this->expectException(TableNotFound::class);
        $this->expectExceptionMessage('The table `bar` is not configured in Dynamap');

        $this->dynamap->get('bar', 123);
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

    public function test float attribute(): void
    {
        $this->dynamap->save(new Article(123));

        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);

        $this->assertEquals(5., $article->getRating());
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

        $this->assertEqualsWithDelta(new \DateTimeImmutable, $article->getCreationDate(), 5);
    }

    public function test nullable date attribute(): void
    {
        // Test it works with a default null value
        $this->dynamap->save(new Article(123));
        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);
        $this->assertNull($article->getPublicationDate());

        // Test that it works when we set a value
        $article->publish();
        $this->dynamap->save($article);
        /** @var Article $article */
        $article = $this->dynamap->get('articles', 123);
        $this->assertEqualsWithDelta(new \DateTimeImmutable, $article->getPublicationDate(), 5);

        // And it still works when we set null again
        $article->unpublish();
        $this->dynamap->save($article);
        $article = $this->dynamap->get('articles', 123);
        $this->assertNull($article->getPublicationDate());
    }
}
