<?php declare(strict_types=1);

namespace Dynamap\Test\Serializer;

use Dynamap\Mapping\Mapping;
use Dynamap\Serializer\EntitySerializer;
use Dynamap\Test\Fixture\Article;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class EntitySerializerTest extends TestCase
{
    private $mapping;

    protected function setUp(): void
    {
        $this->mapping = Mapping::fromConfigArray([
            'tables' => [
                [
                    'name' => 'my_table',
                    'mappings' => [
                        Article::class => [
                            'fields' => [
                                'id' => 'uuid',
                                'name' => 'string',
                                'numComments' => 'integer',
                                'rating' => 'float',
                                'published' => 'boolean',
                                'publishedAt' => 'datetime',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test an entity is seralized(): void
    {
        $serializer = new EntitySerializer($this->mapping);

        $uuid = Uuid::uuid4();

        $article = new Article($uuid);
        $article->setName('Test Article');
        $article->setNumComments(5);
        $article->setRating(3.8);
        $article->publish();

        $result = $serializer->serialize($article);

        $this->assertSame($result['Dynamap\Test\Fixture\Article_id'], $uuid->toString());
        $this->assertSame($result['Dynamap\Test\Fixture\Article_name'], 'Test Article');
        $this->assertSame($result['Dynamap\Test\Fixture\Article_numComments'], 5);
        $this->assertSame($result['Dynamap\Test\Fixture\Article_rating'], 3.8);
        $this->assertTrue($result['Dynamap\Test\Fixture\Article_published']);
        $this->assertSame($result['Dynamap\Test\Fixture\Article_publishedAt'], $article->getPublicationDate()->format(\DateTime::ATOM));
    }

    public function test an entity has non mapped properties serialized(): void
    {
        $serializer = new EntitySerializer($this->mapping);

        $uuid = Uuid::uuid4();
        $authorComment = 'This is a really great article about some tech thing';

        $article = new Article($uuid);
        $article->setName('Test article with unmapped data...');
        $article->setAuthorComment($authorComment);

        $result = $serializer->serialize($article);

        $this->assertSame($authorComment, $result['Dynamap\Test\Fixture\Article_authorComment']);
    }

    public function test an entity is_unserialized(): void
    {
        $serializer = new EntitySerializer($this->mapping);
        $uuid = Uuid::uuid4();

        $article = new Article($uuid);
        $article->setName('Test Article');
        $article->setNumComments(5);
        $article->setRating(3.8);
        $article->setAuthorComment('This is a really great article about some tech thing');
        $article->publish();

        $serializedArticle = $serializer->serialize($article);

        $result = $serializer->unserialize($serializedArticle);

        $this->assertEquals($article->getId(), $result->getId());
        $this->assertSame($article->getName(), $result->getName());
        $this->assertSame($article->getNumComments(), $result->getNumComments());
        $this->assertSame($article->getRating(), $result->getRating());
        $this->assertSame($article->getAuthorComment(), $result->getAuthorComment());
        $this->assertEquals(
            $article->getPublicationDate()->format(\DATE_ATOM),
            $result->getPublicationDate()->format(\DATE_ATOM)
        );
    }
}
