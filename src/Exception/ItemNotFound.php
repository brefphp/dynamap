<?php declare(strict_types=1);

namespace Dynamap\Exception;

class ItemNotFound extends \Exception
{
    /**
     * @param int|string|array $key
     */
    public static function fromKey(string $className, $key): self
    {
        return new self("Item `$className` not found for key " . json_encode($key));
    }
}
