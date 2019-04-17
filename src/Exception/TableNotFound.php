<?php declare(strict_types=1);

namespace Dynamap\Exception;

use Aws\DynamoDb\Exception\DynamoDbException;

/**
 * The table was not found.
 */
class TableNotFound extends \Exception
{
    public static function tableMissingInDynamoDb(string $table, DynamoDbException $previous): self
    {
        $message = "Cannot find the table `$table` in DynamoDB: make sure it exists and that the code has permissions to access it. "
            . $previous->getAwsErrorMessage();

        return new self($message, 0, $previous);
    }
}
