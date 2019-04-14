<?php declare(strict_types=1);

namespace Dynamap;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class Mapping
{
    /** @var TableMapping[] */
    private $tables;

    public function __construct(array $mappingConfig)
    {
        $phpDocExtractor = new PhpDocExtractor;
        $reflectionExtractor = new ReflectionExtractor;
        $listExtractors = [$reflectionExtractor];
        $typeExtractors = [$phpDocExtractor, $reflectionExtractor];
        $descriptionExtractors = [$phpDocExtractor];
        $accessExtractors = [$reflectionExtractor];
        $propertyInitializableExtractors = [$reflectionExtractor];
        $propertyInfo = new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            $descriptionExtractors,
            $accessExtractors,
            $propertyInitializableExtractors
        );

        foreach ($mappingConfig as $tableName => $tableConfig) {
            $this->tables[$tableName] = new TableMapping($propertyInfo, $tableName, $tableConfig);
        }
    }

    public function getTableMapping(string $table): TableMapping
    {
        return $this->tables[$table];
    }

    public function getTableFromClassName(string $className): TableMapping
    {
        foreach ($this->tables as $tableMapping) {
            if ($tableMapping->getClassName() === $className) {
                return $tableMapping;
            }
        }

        throw new \Exception("No table mapping for for class $className");
    }
}
