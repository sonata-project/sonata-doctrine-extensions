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

namespace Sonata\Doctrine\Mapper;

use Sonata\Doctrine\Mapper\Builder\ColumnDefinitionBuilder;
use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;

final class DoctrineCollector
{
    /**
     * @var array<class-string, array<string, array<array<string, mixed>>>>
     */
    private $associations = [];

    /**
     * @var array<class-string, array<string, array<string>>>
     */
    private $indexes = [];

    /**
     * @var array<class-string, array<string, array<string>>>
     */
    private $uniques = [];

    /**
     * @var array<class-string, array<string, class-string>>
     */
    private $discriminators = [];

    /**
     * @var array<class-string, array<string, mixed>>
     */
    private $discriminatorColumns = [];

    /**
     * @var array<class-string, int>
     */
    private $inheritanceTypes = [];

    /**
     * @var array<class-string, array<string, array<array<string, mixed>>>>
     */
    private $overrides = [];

    /**
     * @var DoctrineCollector
     */
    private static $instance;

    private function __construct()
    {
    }

    /**
     * @return DoctrineCollector
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
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
     * @phpstan-param class-string $class
     */
    public function addDiscriminatorColumn(string $class, ColumnDefinitionBuilder $columnDef): void
    {
        if (!isset($this->discriminatorColumns[$class])) {
            $this->discriminatorColumns[$class] = $columnDef->getOptions();
        }
    }

    /**
     * @param int $type
     *
     * @phpstan-param class-string $class
     */
    public function addInheritanceType(string $class, $type): void
    {
        // NEXT_MAJOR: Move int check to method signature
        if (!\is_int($type)) {
            @trigger_error(sprintf(
                'Passing other type than int as argument 2 for method %s() is deprecated since sonata-project/doctrine-extensions 1.8. It will accept only int in version 2.0.',
                __METHOD__
            ), \E_USER_DEPRECATED);
        }

        if (!isset($this->inheritanceTypes[$class])) {
            $this->inheritanceTypes[$class] = $type;
        }
    }

    /**
     * @phpstan-param class-string $class
     */
    public function addAssociation(string $class, string $type, OptionsBuilder $options): void
    {
        if (!isset($this->associations[$class])) {
            $this->associations[$class] = [];
        }

        if (!isset($this->associations[$class][$type])) {
            $this->associations[$class][$type] = [];
        }

        $this->associations[$class][$type][] = $options->getOptions();
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

        if (!isset($this->indexes[$class])) {
            $this->uniques[$class] = [];
        }

        if (isset($this->uniques[$class][$name])) {
            return;
        }

        $this->uniques[$class][$name] = $columns;
    }

    /**
     * @phpstan-param class-string $class
     */
    public function addOverride(string $class, string $type, OptionsBuilder $options): void
    {
        if (!isset($this->overrides[$class])) {
            $this->overrides[$class] = [];
        }

        if (!isset($this->overrides[$class][$type])) {
            $this->overrides[$class][$type] = [];
        }

        $this->overrides[$class][$type][] = $options->getOptions();
    }

    /**
     * @return array<class-string, array<string, array<array<string, mixed>>>>
     */
    public function getAssociations(): array
    {
        return $this->associations;
    }

    /**
     * @return array<class-string, array<string, class-string>>
     */
    public function getDiscriminators(): array
    {
        return $this->discriminators;
    }

    /**
     * @return array<class-string, array<string, mixed>>
     */
    public function getDiscriminatorColumns(): array
    {
        return $this->discriminatorColumns;
    }

    /**
     * @return array<class-string, int>
     */
    public function getInheritanceTypes(): array
    {
        return $this->inheritanceTypes;
    }

    /**
     * @return array<class-string, array<string, array<string>>>
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @return array<class-string, array<string, array<string>>>
     */
    public function getUniques(): array
    {
        return $this->uniques;
    }

    /**
     * @return array<class-string, array<string, array<array<string, mixed>>>>
     */
    public function getOverrides(): array
    {
        return $this->overrides;
    }

    public function clear(): void
    {
        $this->associations = [];
        $this->indexes = [];
        $this->uniques = [];
        $this->discriminatorColumns = [];
        $this->inheritanceTypes = [];
        $this->discriminators = [];
        $this->overrides = [];
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
