<?php declare(strict_types=1);

namespace Dynamap\EntityManager;

use Aws\DynamoDb\DynamoDbClient;
use Dynamap\Mapping\Mapping;
use Dynamap\Serializer\EntitySerializer;

final class EntityManager
{
    /** @var DynamoDbClient */
    private $client;
    /**
     * @var EntitySerializer
     */
    private $serializer;

    public function __construct(DynamoDbClient $client, EntitySerializer $serializer)
    {
        $this->client = $client;

        $this->serializer = $serializer;
    }

    public function persist($entity): void
    {

    }

    public function fetch($entity): object
    {

    }

    public function delete($entity): void
    {

    }

    // create
    // read
    // update
    // delete
}
