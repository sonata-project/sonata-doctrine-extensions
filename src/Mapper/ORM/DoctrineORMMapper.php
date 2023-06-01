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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;
use Doctrine\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\Persistence\Mapping\ClassMetadata;

/**
 * @phpstan-type DiscriminatorColumn = array{
 *     name: string|null,
 *     fieldName?: string,
 *     type?: string,
 *     length?: int,
 *     columnDefinition?: string|null
 * }
 *
 * NEXT_MAJOR: do not implement EventSubscriber interface anymore
 */
final class DoctrineORMMapper implements EventSubscriber
{
    /**
     * @var array<class-string, array<string, array<array<string, mixed>>>>
     */
    private array $associations = [];

    /**
     * @var array<class-string, array<string, class-string>>
     */
    private array $discriminators = [];

    /**
     * @var array<class-string, array<string, mixed>>
     *
     * @phpstan-var array<class-string, DiscriminatorColumn>
     */
    private array $discriminatorColumns = [];

    /**
     * @var array<class-string, int>
     *
     * @phpstan-var array<class-string, ORMClassMetadata::INHERITANCE_TYPE_*>
     */
    private array $inheritanceTypes = [];

    /**
     * @var array<class-string, array<string, array<string>>>
     */
    private array $indexes = [];

    /**
     * @var array<class-string, array<string, array<string>>>
     */
    private array $uniques = [];

    /**
     * @var array<class-string, array<string, array<array<string, mixed>>>>
     */
    private array $overrides = [];

    /**
     * NEXT_MAJOR: remove this method.
     */
    public function getSubscribedEvents(): array
    {
        return [
            'loadClassMetadata',
        ];
    }

    /**
     * @param array<array<string, mixed>> $options
     *
     * @phpstan-param class-string $class
     */
    public function addAssociation(string $class, string $type, array $options): void
    {
        if (!isset($this->associations[$class])) {
            $this->associations[$class] = [];
        }

        $this->associations[$class][$type] = $options;
    }

    /**
     * Add a discriminator to a class.
     *
     * @param string $key Key is the database value and values are the classes
     *
     * @phpstan-param class-string $class
     * @phpstan-param class-string $discriminatorClass
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

    /**
     * @param array<string, mixed> $columnDef
     *
     * @phpstan-param class-string $class
     * @phpstan-param DiscriminatorColumn $columnDef
     */
    public function addDiscriminatorColumn(string $class, array $columnDef): void
    {
        if (!isset($this->discriminatorColumns[$class])) {
            $this->discriminatorColumns[$class] = $columnDef;
        }
    }

    /**
     * @phpstan-param class-string $class
     * @phpstan-param ORMClassMetadata::INHERITANCE_TYPE_* $type
     *
     * @see ClassMetadata for supported types
     */
    public function addInheritanceType(string $class, int $type): void
    {
        if (!isset($this->inheritanceTypes[$class])) {
            $this->inheritanceTypes[$class] = $type;
        }
    }

    /**
     * @param string[] $columns
     *
     * @phpstan-param class-string $class
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
     * @param string[] $columns
     *
     * @phpstan-param class-string $class
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

    /**
     * @param array<array<string, mixed>> $options
     *
     * @phpstan-param class-string $class
     */
    public function addOverride(string $class, string $type, array $options): void
    {
        if (!isset($this->overrides[$class])) {
            $this->overrides[$class] = [];
        }

        $this->overrides[$class][$type] = $options;
    }

    /**
     * @param LoadClassMetadataEventArgs<ClassMetadata<object>, EntityManagerInterface> $eventArgs
     */
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
     * @param ClassMetadata<object> $metadata
     *
     * @throws \RuntimeException
     */
    private function loadAssociations(ClassMetadata $metadata): void
    {
        if (!\array_key_exists($metadata->getName(), $this->associations)) {
            return;
        }

        try {
            foreach ($this->associations[$metadata->getName()] as $type => $mappings) {
                foreach ($mappings as $mapping) {
                    // the association is already set, skip the native one
                    if ($metadata->hasAssociation($mapping['fieldName'])) {
                        continue;
                    }

                    // @phpstan-ignore-next-line https://github.com/phpstan/phpstan/issues/1105
                    \call_user_func([$metadata, $type], $mapping);
                }
            }
        } catch (\ReflectionException $e) {
            throw new \RuntimeException(sprintf('Error with class %s : %s', $metadata->getName(), $e->getMessage()), 404, $e);
        }
    }

    /**
     * @param ClassMetadata<object> $metadata
     *
     * @throws \RuntimeException
     */
    private function loadDiscriminatorColumns(ClassMetadata $metadata): void
    {
        if (!\array_key_exists($metadata->getName(), $this->discriminatorColumns)) {
            return;
        }

        \assert($metadata instanceof ORMClassMetadata);

        try {
            if (isset($this->discriminatorColumns[$metadata->getName()])) {
                $arrayDiscriminatorColumns = $this->discriminatorColumns[$metadata->getName()];
                if (isset($metadata->discriminatorColumn)) {
                    $arrayDiscriminatorColumns = array_merge($metadata->discriminatorColumn, $this->discriminatorColumns[$metadata->getName()]);
                }
                $metadata->setDiscriminatorColumn($arrayDiscriminatorColumns);
            }
        } catch (\ReflectionException $e) {
            throw new \RuntimeException(sprintf('Error with class %s : %s', $metadata->getName(), $e->getMessage()), 404, $e);
        }
    }

    /**
     * @param ClassMetadata<object> $metadata
     *
     * @throws \RuntimeException
     */
    private function loadInheritanceTypes(ClassMetadata $metadata): void
    {
        if (!\array_key_exists($metadata->getName(), $this->inheritanceTypes)) {
            return;
        }

        \assert($metadata instanceof ORMClassMetadata);

        try {
            if (isset($this->inheritanceTypes[$metadata->getName()])) {
                $metadata->setInheritanceType($this->inheritanceTypes[$metadata->getName()]);
            }
        } catch (\ReflectionException $e) {
            throw new \RuntimeException(sprintf('Error with class %s : %s', $metadata->getName(), $e->getMessage()), 404, $e);
        }
    }

    /**
     * @param ClassMetadata<object> $metadata
     *
     * @throws \RuntimeException
     */
    private function loadDiscriminators(ClassMetadata $metadata): void
    {
        if (!\array_key_exists($metadata->getName(), $this->discriminators)) {
            return;
        }

        \assert($metadata instanceof ORMClassMetadata);

        try {
            foreach ($this->discriminators[$metadata->getName()] as $key => $class) {
                if (\in_array($key, $metadata->discriminatorMap, true)) {
                    continue;
                }
                $metadata->setDiscriminatorMap([$key => $class]);
            }
        } catch (\ReflectionException $e) {
            throw new \RuntimeException(sprintf('Error with class %s : %s', $metadata->getName(), $e->getMessage()), 404, $e);
        }
    }

    /**
     * @param ClassMetadata<object> $metadata
     */
    private function loadIndexes(ClassMetadata $metadata): void
    {
        if (!\array_key_exists($metadata->getName(), $this->indexes)) {
            return;
        }

        \assert($metadata instanceof ORMClassMetadata);

        foreach ($this->indexes[$metadata->getName()] as $name => $columns) {
            $metadata->table['indexes'][$name] = ['columns' => $columns];
        }
    }

    /**
     * @param ClassMetadata<object> $metadata
     */
    private function loadUniques(ClassMetadata $metadata): void
    {
        if (!\array_key_exists($metadata->getName(), $this->uniques)) {
            return;
        }

        \assert($metadata instanceof ORMClassMetadata);

        foreach ($this->uniques[$metadata->getName()] as $name => $columns) {
            $metadata->table['uniqueConstraints'][$name] = ['columns' => $columns];
        }
    }

    /**
     * @param ClassMetadata<object> $metadata
     */
    private function loadOverrides(ClassMetadata $metadata): void
    {
        if (!\array_key_exists($metadata->getName(), $this->overrides)) {
            return;
        }

        try {
            foreach ($this->overrides[$metadata->getName()] as $type => $overrides) {
                foreach ($overrides as $override) {
                    // @phpstan-ignore-next-line https://github.com/phpstan/phpstan/issues/1105
                    \call_user_func([$metadata, $type], $override['fieldName'], $override);
                }
            }
        } catch (\ReflectionException $e) {
            throw new \RuntimeException(
                sprintf('Error with class %s : %s', $metadata->getName(), $e->getMessage()),
                404,
                $e
            );
        }
    }

    /**
     * @param string[] $columns
     */
    private function verifyColumnNames(array $columns): void
    {
        foreach ($columns as $column) {
            if (!\is_string($column)) {
                throw new \InvalidArgumentException(sprintf('The column is not a valid string, %s given', \gettype($column)));
            }
        }
    }
}
