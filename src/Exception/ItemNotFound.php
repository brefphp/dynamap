<?php declare(strict_types=1);

namespace Dynamap\Exception;

class ItemNotFound extends \Exception
{
    public static function fromKey(string $className, int|array|string $key): self
    {
        return new self("Item `$className` not found for key " . json_encode($key, JSON_THROW_ON_ERROR));
    }
}
