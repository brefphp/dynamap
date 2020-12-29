<?php declare(strict_types=1);

namespace Dynamap\Test;

use DateTimeImmutable;
use Dynamap\Dynamap;
use Dynamap\Test\Fixture\Annotations;
use stdClass;

class AnnotationsTest extends FunctionalTest
{
    private Dynamap $dynamap;

    public function setUp(): void
    {
        parent::setUp();

        $mapping = [
            Annotations::class => [
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
        $article = new Annotations(123);
        $article->setName('Hello world!');
        $this->dynamap->save($article);

        /** @var Annotations $article */
        $article = $this->dynamap->get(Annotations::class, 123);

        $this->assertEquals('Hello world!', $article->getName());
    }

    public function test float attribute(): void
    {
        $this->dynamap->save(new Annotations(123));

        /** @var Annotations $article */
        $article = $this->dynamap->get(Annotations::class, 123);

        $this->assertEquals(5., $article->getRating());
    }

    public function test update existing object(): void
    {
        $article = new Annotations(123);
        $article->setName('Hello world!');
        $this->dynamap->save($article);

        /** @var Annotations $article */
        $article = $this->dynamap->get(Annotations::class, 123);

        $article->setName('Hello John!');
        $this->dynamap->save($article);

        /** @var Annotations $article */
        $article = $this->dynamap->get(Annotations::class, 123);

        $this->assertEquals('Hello John!', $article->getName());
    }

    public function test boolean attribute(): void
    {
        $article = new Annotations(123);
        $this->dynamap->save($article);

        /** @var Annotations $article */
        $article = $this->dynamap->get(Annotations::class, 123);
        $this->assertFalse($article->isPublished());

        $article->publish();
        $this->dynamap->save($article);

        /** @var Annotations $article */
        $article = $this->dynamap->get(Annotations::class, 123);
        $this->assertTrue($article->isPublished());
    }

    public function test date attribute(): void
    {
        $this->dynamap->save(new Annotations(123));

        /** @var Annotations $article */
        $article = $this->dynamap->get(Annotations::class, 123);

        $this->assertEqualsWithDelta(new DateTimeImmutable, $article->getCreationDate(), 5);
    }

    public function test nullable date attribute(): void
    {
        // Test it works with a default null value
        $this->dynamap->save(new Annotations(123));
        /** @var Annotations $article */
        $article = $this->dynamap->get(Annotations::class, 123);
        $this->assertNull($article->getPublicationDate());

        // Test that it works when we set a value
        $article->publish();
        $this->dynamap->save($article);
        /** @var Annotations $article */
        $article = $this->dynamap->get(Annotations::class, 123);
        $this->assertEqualsWithDelta(new DateTimeImmutable, $article->getPublicationDate(), 5);

        // And it still works when we set null again
        $article->unpublish();
        $this->dynamap->save($article);
        $article = $this->dynamap->get(Annotations::class, 123);
        $this->assertNull($article->getPublicationDate());
    }
}
