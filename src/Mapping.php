<?php declare(strict_types=1);

namespace Dynamap;

use Dynamap\Exception\TableNotFound;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class Mapping
{
    /** @var array|TableMapping[] */
    private array $tables;
    private ?PropertyInfoExtractor $propertyInfo = null;

    public function __construct(array $mappingConfig)
    {
        $this->tables = $mappingConfig; // lazy initialization
    }

    public function getTableMapping(string $className): TableMapping
    {
        if (! isset($this->tables[$className])) {
            throw new TableNotFound("No table mapping found for class `$className`");
        }

        if (is_array($this->tables[$className])) {
            $mapping = new TableMapping($this->propertyInfo(), $className, $this->tables[$className]);
            $this->tables[$className] = $mapping;
        }

        return $this->tables[$className];
    }

    private function propertyInfo(): PropertyInfoExtractor
    {
        if (! $this->propertyInfo) {
            $phpDocExtractor = new PhpDocExtractor;
            $reflectionExtractor = new ReflectionExtractor;
            $listExtractors = [$reflectionExtractor];
            $typeExtractors = [$phpDocExtractor, $reflectionExtractor];
            $descriptionExtractors = [$phpDocExtractor];
            $accessExtractors = [$reflectionExtractor];
            $propertyInitializableExtractors = [$reflectionExtractor];
            $this->propertyInfo = new PropertyInfoExtractor(
                $listExtractors,
                $typeExtractors,
                $descriptionExtractors,
                $accessExtractors,
                $propertyInitializableExtractors
            );
        }

        return $this->propertyInfo;
    }
}
