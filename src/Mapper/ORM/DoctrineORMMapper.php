<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Doctrine\Mapper\ORM;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use InvalidArgumentException;
use ReflectionException;
use RuntimeException;
use Sonata\Doctrine\Mapper\Builder\ColumnDefinitionBuilder;
use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;

final class DoctrineORMMapper implements EventSubscriber
{
    /**
     * @var array
     */
    private $associations = [];

    /**
     * @var array
     */
    private $discriminators = [];

    /**
     * @var array
     */
    private $discriminatorColumns = [];

    /**
     * @var array
     */
    private $inheritanceTypes = [];

    /**
     * @var array
     */
    private $indexes = [];

    /**
     * @var array
     */
    private $uniques = [];

    /**
     * @var array
     */
    private $overrides = [];

    public function getSubscribedEvents(): array
    {
        return [
            'loadClassMetadata',
        ];
    }

    public function addAssociation(string $class, string $field, OptionsBuilder $options): void
    {
        if (!isset($this->associations[$class])) {
            $this->associations[$class] = [];
        }

        $this->associations[$class][$field] = $options->getOptions();
    }

    /**
     * Add a discriminator to a class.
     *
     * @param string $key                Key is the database value and values are the classes
     * @param string $discriminatorClass The mapped class
     */
    public function addDiscriminator(string $class, string $key, string $discriminatorClass): void
    {
        if (!isset($this->discriminators[$class])) {
            $this->discriminators[$class] = [];
        }

        if (!isset($this->discriminators[$class][$key])) {
            $this->discriminators[$class][$key] = $discriminatorClass;
        }
    }

    public function addDiscriminatorColumn(string $class, ColumnDefinitionBuilder $columnDef): void
    {
        if (!isset($this->discriminatorColumns[$class])) {
            $this->discriminatorColumns[$class] = $columnDef->getOptions();
        }
    }

    /**
     * @see ClassMetadataInfo for supported types
     */
    public function addInheritanceType(string $class, int $type): void
    {
        if (!isset($this->inheritanceTypes[$class])) {
            $this->inheritanceTypes[$class] = $type;
        }
    }

    /**
     * @param array<string> $columns
     */
    public function addIndex(string $class, string $name, array $columns): void
    {
        $this->verifyColumnNames($columns);

        if (!isset($this->indexes[$class])) {
            $this->indexes[$class] = [];
        }

        if (isset($this->indexes[$class][$name])) {
            return;
        }

        $this->indexes[$class][$name] = $columns;
    }

    /**
     * @param array<string> $columns
     */
    public function addUnique(string $class, string $name, array $columns): void
    {
        $this->verifyColumnNames($columns);

        if (!isset($this->uniques[$class])) {
            $this->uniques[$class] = [];
        }

        if (isset($this->uniques[$class][$name])) {
            return;
        }

        $this->uniques[$class][$name] = $columns;
    }

    public function addOverride(string $class, string $type, OptionsBuilder $options): void
    {
        if (!isset($this->overrides[$class])) {
            $this->overrides[$class] = [];
        }

        $this->overrides[$class][$type] = $options->getOptions();
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();

        $this->loadAssociations($metadata);
        $this->loadIndexes($metadata);
        $this->loadUniques($metadata);

        $this->loadDiscriminatorColumns($metadata);
        $this->loadDiscriminators($metadata);
        $this->loadInheritanceTypes($metadata);
        $this->loadOverrides($metadata);
    }

    /**
     * @throws RuntimeException
     */
    private function loadAssociations(ClassMetadataInfo $metadata): void
    {
        if (!\array_key_exists($metadata->name, $this->associations)) {
            return;
        }

        try {
            foreach ($this->associations[$metadata->name] as $type => $mappings) {
                foreach ($mappings as $mapping) {
                    // the association is already set, skip the native one
                    if ($metadata->hasAssociation($mapping['fieldName'])) {
                        continue;
                    }

                    \call_user_func([$metadata, $type], $mapping);
                }
            }
        } catch (ReflectionException $e) {
            throw new RuntimeException(sprintf('Error with class %s : %s', $metadata->name, $e->getMessage()), 404, $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    private function loadDiscriminatorColumns(ClassMetadataInfo $metadata): void
    {
        if (!\array_key_exists($metadata->name, $this->discriminatorColumns)) {
            return;
        }

        try {
            if (isset($this->discriminatorColumns[$metadata->name])) {
                $arrayDiscriminatorColumns = $this->discriminatorColumns[$metadata->name];
                if (isset($metadata->discriminatorColumn)) {
                    $arrayDiscriminatorColumns = array_merge($metadata->discriminatorColumn, $this->discriminatorColumns[$metadata->name]);
                }
                $metadata->setDiscriminatorColumn($arrayDiscriminatorColumns);
            }
        } catch (ReflectionException $e) {
            throw new RuntimeException(sprintf('Error with class %s : %s', $metadata->name, $e->getMessage()), 404, $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    private function loadInheritanceTypes(ClassMetadataInfo $metadata): void
    {
        if (!\array_key_exists($metadata->name, $this->inheritanceTypes)) {
            return;
        }

        try {
            if (isset($this->inheritanceTypes[$metadata->name])) {
                $metadata->setInheritanceType($this->inheritanceTypes[$metadata->name]);
            }
        } catch (ReflectionException $e) {
            throw new RuntimeException(sprintf('Error with class %s : %s', $metadata->name, $e->getMessage()), 404, $e);
        }
    }

    /**
     * @throws RuntimeException
     */
    private function loadDiscriminators(ClassMetadataInfo $metadata): void
    {
        if (!\array_key_exists($metadata->name, $this->discriminators)) {
            return;
        }

        try {
            foreach ($this->discriminators[$metadata->name] as $key => $class) {
                if (\in_array($key, $metadata->discriminatorMap, true)) {
                    continue;
                }
                $metadata->setDiscriminatorMap([$key => $class]);
            }
        } catch (ReflectionException $e) {
            throw new RuntimeException(sprintf('Error with class %s : %s', $metadata->name, $e->getMessage()), 404, $e);
        }
    }

    private function loadIndexes(ClassMetadataInfo $metadata): void
    {
        if (!\array_key_exists($metadata->name, $this->indexes)) {
            return;
        }

        foreach ($this->indexes[$metadata->name] as $name => $columns) {
            $metadata->table['indexes'][$name] = ['columns' => $columns];
        }
    }

    private function loadUniques(ClassMetadataInfo $metadata): void
    {
        if (!\array_key_exists($metadata->name, $this->uniques)) {
            return;
        }

        foreach ($this->uniques[$metadata->name] as $name => $columns) {
            $metadata->table['uniqueConstraints'][$name] = ['columns' => $columns];
        }
    }

    private function loadOverrides(ClassMetadataInfo $metadata): void
    {
        if (!\array_key_exists($metadata->name, $this->overrides)) {
            return;
        }

        try {
            foreach ($this->overrides[$metadata->name] as $type => $overrides) {
                foreach ($overrides as $override) {
                    \call_user_func([$metadata, $type], $override['fieldName'], $override);
                }
            }
        } catch (ReflectionException $e) {
            throw new RuntimeException(
                sprintf('Error with class %s : %s', $metadata->name, $e->getMessage()), 404, $e
            );
        }
    }

    private function verifyColumnNames(array $columns): void
    {
        foreach ($columns as $column) {
            if (!\is_string($column)) {
                throw new InvalidArgumentException(sprintf('The column is not a valid string, %s given', \gettype($column)));
            }
        }
    }
}
