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

        $this->assertSame($result['Article_id'], $uuid->toString());
        $this->assertSame($result['Article_name'], 'Test Article');
        $this->assertSame($result['Article_numComments'], 5);
        $this->assertSame($result['Article_rating'], 3.8);
        $this->assertTrue($result['Article_published']);
        $this->assertSame($result['Article_publishedAt'], $article->getPublicationDate()->format(\DateTime::ATOM));
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

        $this->assertSame($authorComment, $result['Article_authorComment']);
    }
}
