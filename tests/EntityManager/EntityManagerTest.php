<?php

namespace Dynamap\Test\EntityManager;

use Aws\DynamoDb\DynamoDbClient;
use Dynamap\EntityManager\EntityManager;
use Dynamap\Mapping\Mapping;
use Dynamap\Serializer\EntitySerializer;
use Dynamap\Test\Fixture\Article;
use PHPUnit\Framework\TestCase;

;

class EntityManagerTest extends TestCase
{
    /**
     * @var DynamoDbClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;
    /**
     * @var EntitySerializer
     */
    private $serializer;

    protected function setUp(): void
    {
        $this->client = $this->createMock(DynamoDbClient::class);
        $this->serializer = new EntitySerializer(
            Mapping::fromConfigArray([
                'tables' => [
                    [
                        'name' => 'my_table',
                        'mappings' => [
                            Article::class => [
                                'keys' => [
                                    'id' => 'integer'
                                ]
                            ]
                        ]
                    ]
                ]
            ])
        );
    }

    public function test persisting new entity(): void
    {
        $em = new EntityManager($this->client, $this->serializer);

        $article = new Article();

        $em->persist($article);
        $this->assertSame(1, 1);
    }
}