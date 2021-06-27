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

namespace Sonata\Doctrine\Test;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;

trait EntityManagerMockFactoryTrait
{
    /**
     * @param string[] $fields
     *
     * @return EntityManagerInterface|MockObject
     */
    final protected function createEntityManagerMock(\Closure $qbCallback, array $fields): MockObject
    {
        $query = $this->createMock(AbstractQuery::class);
        $query->method('execute')->willReturn(true);
        $query->method('getResult')->willReturn([]);
        $query->method('getOneOrNullResult')->willReturn(null);

        $qb = $this->createMock(QueryBuilder::class);

        $qb->method('getQuery')->willReturn($query);
        $qb->method('distinct')->willReturn($qb);
        $qb->method('from')->willReturn($qb);
        $qb->method('select')->willReturn($qb);
        $qb->method('addSelect')->willReturn($qb);
        $qb->method('where')->willReturn($qb);
        $qb->method('andWhere')->willReturn($qb);
        $qb->method('orWhere')->willReturn($qb);
        $qb->method('setParameter')->willReturn($qb);
        $qb->method('setParameters')->willReturn($qb);
        $qb->method('setFirstResult')->willReturn($qb);
        $qb->method('setMaxResults')->willReturn($qb);
        $qb->method('groupBy')->willReturn($qb);
        $qb->method('addGroupBy')->willReturn($qb);
        $qb->method('having')->willReturn($qb);
        $qb->method('andHaving')->willReturn($qb);
        $qb->method('orHaving')->willReturn($qb);
        $qb->method('orderBy')->willReturn($qb);
        $qb->method('addOrderBy')->willReturn($qb);
        $qb->method('join')->willReturn($qb);
        $qb->method('innerJoin')->willReturn($qb);
        $qb->method('leftJoin')->willReturn($qb);

        $qbCallback($qb);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('createQueryBuilder')->willReturn($qb);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getFieldNames')->willReturn($fields);
        $metadata->method('getName')->willReturn('className');

        $em = $this->createMock(EntityManager::class);
        $em->method('getRepository')->willReturn($repository);
        $em->method('getClassMetadata')->willReturn($metadata);

        return $em;
    }
}
