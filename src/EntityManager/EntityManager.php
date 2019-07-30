<?php

namespace Dynamap\EntityManager;

use Aws\DynamoDb\DynamoDbClient;
use Dynamap\Mapping;

final class EntityManager
{
    /**
     * @var Mapping
     */
    private $mapping;

    /**
     * @var DynamoDbClient
     */
    private $client;

    public function __construct(DynamoDbClient $client, Mapping $mapping)
    {
        $this->mapping = $mapping;
        $this->client = $client;
    }

    // create
    // read
    // update
    // delete
}
