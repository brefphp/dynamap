<?php declare(strict_types=1);

namespace Dynamap\Test;

use DateTimeImmutable;
use Dynamap\Dynamap;
use Dynamap\Test\Fixture\Types;
use stdClass;

class TypesTest extends FunctionalTest
{
    private Dynamap $dynamap;

    public function setUp(): void
    {
        parent::setUp();

        $mapping = [
            Types::class => [
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

    public function test string attribute(): void
    {
        $article = new Types(123);
        $article->name = 'Hello world!';
        $this->dynamap->save($article);

        /** @var Types $article */
        $article = $this->dynamap->get(Types::class, 123);

        $this->assertEquals('Hello world!', $article->name);
    }

    public function test float attribute(): void
    {
        $this->dynamap->save(new Types(123));

        /** @var Types $article */
        $article = $this->dynamap->get(Types::class, 123);

        $this->assertEquals(5., $article->rating);
    }

    public function test update existing object(): void
    {
        $article = new Types(123);
        $article->name = 'Hello world!';
        $this->dynamap->save($article);

        /** @var Types $article */
        $article = $this->dynamap->get(Types::class, 123);

        $article->name = 'Hello John!';
        $this->dynamap->save($article);

        /** @var Types $article */
        $article = $this->dynamap->get(Types::class, 123);

        $this->assertEquals('Hello John!', $article->name);
    }

    public function test boolean attribute(): void
    {
        $article = new Types(123);
        $this->dynamap->save($article);

        /** @var Types $article */
        $article = $this->dynamap->get(Types::class, 123);
        $this->assertFalse($article->published);

        $article->publish();
        $this->dynamap->save($article);

        /** @var Types $article */
        $article = $this->dynamap->get(Types::class, 123);
        $this->assertTrue($article->published);
    }

    public function test date attribute(): void
    {
        $this->dynamap->save(new Types(123));

        /** @var Types $article */
        $article = $this->dynamap->get(Types::class, 123);

        $this->assertEqualsWithDelta(new DateTimeImmutable, $article->createdAt, 5);
    }

    public function test nullable date attribute(): void
    {
        // Test it works with a default null value
        $this->dynamap->save(new Types(123));
        /** @var Types $article */
        $article = $this->dynamap->get(Types::class, 123);
        $this->assertNull($article->publishedAt);

        // Test that it works when we set a value
        $article->publish();
        $this->dynamap->save($article);
        /** @var Types $article */
        $article = $this->dynamap->get(Types::class, 123);
        $this->assertEqualsWithDelta(new DateTimeImmutable, $article->publishedAt, 5);

        // And it still works when we set null again
        $article->unpublish();
        $this->dynamap->save($article);
        $article = $this->dynamap->get(Types::class, 123);
        $this->assertNull($article->publishedAt);
    }
}
