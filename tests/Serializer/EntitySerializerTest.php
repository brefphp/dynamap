<?php

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
                                'publishedAt' => 'datetime'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function test an entity is seralized(): void
    {
        $serializer = new EntitySerializer($this->mapping);

        $uuid = Uuid::uuid4();

        $article = new Article($uuid);
        $article->setName('Test article');
        $article->setNumComments(5);
        $article->setRating(3.8);
        $article->publish();

        $result = $serializer->serialize($article);
        $this->assertSame($result['Article_id'], $uuid->toString());
        $this->assertSame($result['Article_name'], 'Test Article');
        $this->assertSame($result['Article_numComments'], 5);
        $this->assertSame($result['Article_rating'], 3.8);
        $this->assertTrue($result['Article_published']);
        $this->assertSame($result['Article_publishedAt'], $article->getPublicationDate());
    }
}