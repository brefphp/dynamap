<?php declare(strict_types=1);

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\NoTableSpeficiedException;

final class Mapping
{
    /** @var array */
    private $mapping = [];

    private function __construct(array $mapping)
    {
        var_dump($mapping);
    }

    public static function fromConfigArray(array $config)
    {
        if (\array_key_exists('tables', $config) === false || empty($config['tables'])) {
            throw new NoTableSpeficiedException('Dynamap needs at least one table to work with!');
        }

        $mapping = \array_reduce($config['tables'], static function ($carry, $item) {
            $carry[] = TableMapping::fromArray($item);
            return $carry;
        }, []);

        return new static($mapping);
    }
}
