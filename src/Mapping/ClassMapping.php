<?php

namespace Dynamap\Mapping;

use Dynamap\Mapping\Exception\ClassNameInvalidException;

class ClassMapping
{
    /**
     * @var string
     */
    private $className;
    /**
     * @var array
     */
    private $config;

    private function __construct(string $className, array $config)
    {
        $this->className = $className;
        $this->config = $config;
    }

    public static function fromArray(string $className, array $config): ClassMapping
    {
        if (false === \class_exists($className)) {
            throw new ClassNameInvalidException('Could not map ' . $className . ' as the class was not found');
        }

        return new static($className, $config);
    }
}