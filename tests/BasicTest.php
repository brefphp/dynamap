<?php declare(strict_types=1);

namespace Dynamap\Test;

use Dynamap\Dynamap;
use Dynamap\Exception\ItemNotFound;
use Dynamap\Exception\TableNotFound;
use Dynamap\Test\Fixture\Article;
use stdClass;

class BasicTest extends FunctionalTest
{
    private Dynamap $dynamap;

    public function setUp(): void
    {
        parent::setUp();

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
            stdClass::class => [
                'table' => 'foo', // This is a table that doesn't exist
                'keys' => [
                    'id',
                ],
            ],
        ];
        $this->dynamap = new Dynamap($this->dynamoDb, $mapping);
    }

    public function test store and retrieve object(): void
    {
        $this->dynamap->save(new Article(123));

        /** @var Article $article */
        $article = $this->dynamap->get(Article::class, 123);

        $this->assertEquals(123, $article->id);
    }

    public function test get empty table(): void
    {
        $this->assertEmpty($this->dynamap->getAll(Article::class));
    }

    public function test get all(): void
    {
        $this->dynamap->save(new Article(123));
        $this->dynamap->save(new Article(456));
        $this->dynamap->save(new Article(789));

        $this->assertCount(3, $this->dynamap->getAll(Article::class));
    }

    public function test find unknown object(): void
    {
        $this->assertNull($this->dynamap->find(Article::class, 123));
    }

    public function test get unknown object(): void
    {
        $this->expectException(ItemNotFound::class);
        $this->expectExceptionMessage('Item `Dynamap\Test\Fixture\Article` not found for key 123');

        $this->dynamap->get(Article::class, 123);
    }

    public function test with unknown class(): void
    {
        $this->expectExceptionMessage('The class `UnknownClass` doesn\'t exist');

        $this->dynamap->get('UnknownClass', 123);
    }

    public function test get table that is mapped but doesnt exist in DynamoDB(): void
    {
        $this->expectException(TableNotFound::class);
        $this->expectExceptionMessage('Cannot find the table `foo` in DynamoDB: make sure it exists and that the code has permissions to access it');

        $this->dynamap->get(stdClass::class, 123);
    }

    public function test get unknown class(): void
    {
        $this->expectException(TableNotFound::class);
        $this->expectExceptionMessage('No table mapping found for class `Foo`');

        $this->dynamap->get('Foo', 123);
    }
}
