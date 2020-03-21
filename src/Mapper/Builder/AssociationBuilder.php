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

namespace Sonata\Doctrine\Mapper\Builder;

final class AssociationBuilder implements OptionsBuilderInterface
{
    private const ONE_TO_ONE = 'one_to_one';
    private const ONE_TO_MANY = 'one_to_many';
    private const MANY_TO_ONE = 'many_to_one';
    private const MANY_TO_MANY = 'many_to_many';

    /**
     * @var array<string, mixed>
     */
    private $options = [];

    /**
     * @var string
     */
    private $type;

    private function __construct(string $type, string $fieldName, string $targetEntity)
    {
        $this->type = $type;
        $this->options['fieldName'] = $fieldName;
        $this->options['targetEntity'] = $targetEntity;
    }

    public function add(string $key, $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    public static function createOneToOne(string $fieldName, string $targetEntity): self
    {
        return new self(self::ONE_TO_ONE, $fieldName, $targetEntity);
    }

    public static function createOneToMany(string $fieldName, string $targetEntity): self
    {
        return new self(self::ONE_TO_MANY, $fieldName, $targetEntity);
    }

    public static function createManyToOne(string $fieldName, string $targetEntity): self
    {
        return new self(self::MANY_TO_ONE, $fieldName, $targetEntity);
    }

    public static function createManyToMany(string $fieldName, string $targetEntity): self
    {
        return new self(self::MANY_TO_MANY, $fieldName, $targetEntity);
    }

    public function mappedBy(string $mappedBy): self
    {
        if (!\in_array($this->type, [self::ONE_TO_MANY, self::ONE_TO_ONE, self::MANY_TO_MANY], true)) {
            throw new \RuntimeException(
                'Invalid option, mappedBy only applies to one-to-many and one-to-one associations'
            );
        }

        $this->options['mappedBy'] = $mappedBy;

        return $this;
    }

    public function inversedBy(string $inversedBy): self
    {
        if (!\in_array($this->type, [self::ONE_TO_ONE, self::MANY_TO_ONE, self::MANY_TO_MANY], true)) {
            throw new \RuntimeException(
                'Invalid option: inversedBy only applies to one-to-one, many-to-one or many-to-many associations'
            );
        }

        $this->options['inversedBy'] = $inversedBy;

        return $this;
    }

    /**
     * @param array{
     *     name: string,
     *     referencedColumnName: string,
     *     unique?: bool,
     *     nullable?: bool,
     *     onDelete?: string,
     *     columnDefinition?: string
     * }[] $joinColumns
     * @param array{
     *     name: string,
     *     referencedColumnName: string,
     *     unique?: bool,
     *     nullable?: bool,
     *     onDelete?: string,
     *     columnDefinition?: string
     * }[] $inverseJoinColumns
     */
    public function addJoinTable(string $name, array $joinColumns, array $inverseJoinColumns): self
    {
        if (self::MANY_TO_MANY !== $this->type) {
            throw new \RuntimeException('Invalid mapping, joinTables only apply to many-to-many associations');
        }

        $this->options['joinTable'] = [
            'name' => $name,
            'joinColumns' => $joinColumns,
            'inverseJoinColumns' => $inverseJoinColumns,
        ];

        return $this;
    }

    /**
     * @param 'ASC'|'DESC' $orientation
     */
    public function addOrder(string $field, string $orientation): self
    {
        if (!\in_array($this->type, [self::ONE_TO_MANY, self::MANY_TO_MANY], true)) {
            throw new \RuntimeException(
                'Invalid option: orderBy only applies to one-to-many or many-to-many associations'
            );
        }

        if (!isset($this->options['orderBy'])) {
            $this->options['orderBy'] = [];
        }

        $this->options['orderBy'] = array_merge($this->options['orderBy'], [$field => $orientation]);

        return $this;
    }

    /**
     * @param array{
     *     name: string,
     *     referencedColumnName: string,
     *     unique?: bool,
     *     nullable?: bool,
     *     onDelete?: string,
     *     columnDefinition?: string
     * } $joinColumn
     */
    public function addJoinColumn(array $joinColumn): self
    {
        if (!\in_array($this->type, [self::MANY_TO_ONE, self::ONE_TO_ONE], true)) {
            throw new \RuntimeException(
                'Invalid option, joinColumns only apply to many-to-one and one-to-one associations'
            );
        }

        if (!isset($this->options['joinColumns'])) {
            $this->options['joinColumns'] = [];
        }

        $this->options['joinColumns'][] = $joinColumn;

        return $this;
    }

    /**
     * @psalm-param list<'persist'|'remove'|'merge'|'detach'|'refresh'|'all'> $value
     */
    public function cascade(array $value): self
    {
        $this->options['cascade'] = $value;

        return $this;
    }

    public function orphanRemoval(): self
    {
        if (!\in_array($this->type, [self::ONE_TO_ONE, self::ONE_TO_MANY], true)) {
            throw new \RuntimeException(
                'Invalid option, orphanRemoval only apply to one-to-one and one-to-many associations'
            );
        }

        $this->options['orphanRemoval'] = true;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
