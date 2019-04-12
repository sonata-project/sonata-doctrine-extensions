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

use InvalidArgumentException;
use Sonata\Doctrine\Mapper\Builder\ColumnDefinitionBuilder;
use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;

final class DoctrineCollector
{
    /**
     * @var array
     */
    private $associations = [];

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
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
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
            $this->discriminatorColumns[$class] = $columnDef;
        }
    }

    public function addInheritanceType(string $class, string $type): void
    {
        if (!isset($this->inheritanceTypes[$class])) {
            $this->inheritanceTypes[$class] = $type;
        }
    }

    public function addAssociation(string $class, string $type, OptionsBuilder $options): void
    {
        if (!isset($this->associations[$class])) {
            $this->associations[$class] = [];
        }

        if (!isset($this->associations[$class][$type])) {
            $this->associations[$class][$type] = [];
        }

        $this->associations[$class][$type][] = $options;
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

        if (!isset($this->indexes[$class])) {
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

        if (!isset($this->overrides[$class][$type])) {
            $this->overrides[$class][$type] = [];
        }

        $this->overrides[$class][$type][] = $options;
    }

    public function getAssociations(): array
    {
        return $this->associations;
    }

    public function getDiscriminators(): array
    {
        return $this->discriminators;
    }

    public function getDiscriminatorColumns(): array
    {
        return $this->discriminatorColumns;
    }

    public function getInheritanceTypes(): array
    {
        return $this->inheritanceTypes;
    }

    public function getIndexes(): array
    {
        return $this->indexes;
    }

    public function getUniques(): array
    {
        return $this->uniques;
    }

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

    private function verifyColumnNames(array $columns): void
    {
        foreach ($columns as $column) {
            if (!\is_string($column)) {
                throw new InvalidArgumentException(sprintf('The column is not a valid string, %s given', \gettype($column)));
            }
        }
    }
}
