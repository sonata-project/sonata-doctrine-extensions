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

namespace Sonata\Doctrine\Model;

/**
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 *
 * @phpstan-template T of object
 */
interface ManagerInterface
{
    /**
     * Return the Entity class name.
     *
     * @phpstan-return class-string<T>
     */
    public function getClass(): string;

    /**
     * Find all entities in the repository.
     *
     * @return object[]
     *
     * @phpstan-return T[]
     */
    public function findAll(): array;

    /**
     * Find entities by a set of criteria.
     *
     * @param array<string, mixed>                          $criteria
     * @param array<string, 'asc'|'ASC'|'desc'|'DESC'>|null $orderBy
     *
     * @return object[]
     *
     * @phpstan-return T[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * Find a single entity by a set of criteria.
     *
     * @param array<string, mixed> $criteria
     *
     * @phpstan-return T|null
     */
    public function findOneBy(array $criteria): ?object;

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier
     *
     * @phpstan-return T|null
     */
    public function find($id): ?object;

    /**
     * Create an empty Entity instance.
     *
     * @phpstan-return T
     */
    public function create(): object;

    /**
     * Save an Entity.
     *
     * @param object $entity   The Entity to save
     * @param bool   $andFlush Flush the EntityManager after saving the object?
     *
     * @phpstan-param T $entity
     */
    public function save(object $entity, bool $andFlush = true): void;

    /**
     * Delete an Entity.
     *
     * @param object $entity   The Entity to delete
     * @param bool   $andFlush Flush the EntityManager after deleting the object?
     *
     * @phpstan-param T $entity
     */
    public function delete(object $entity, bool $andFlush = true): void;
}
