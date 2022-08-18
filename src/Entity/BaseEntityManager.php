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

namespace Sonata\Doctrine\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Sonata\Doctrine\Exception\TransactionException;
use Sonata\Doctrine\Model\BaseManager;
use Sonata\Doctrine\Model\TransactionalManagerInterface;

/**
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 *
 * @phpstan-template T of object
 * @phpstan-extends BaseManager<T>
 */
abstract class BaseEntityManager extends BaseManager implements TransactionalManagerInterface
{
    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): ObjectManager
    {
        $objectManager = $this->getObjectManager();
        \assert($objectManager instanceof EntityManagerInterface);

        return $objectManager;
    }

    public function beginTransaction(): void
    {
        $this->getEntityManager()->beginTransaction();
    }

    public function commit(): void
    {
        try {
            $this->getEntityManager()->commit();
        } catch (\Throwable $exception) {
            throw new TransactionException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }

    public function rollBack(): void
    {
        $this->getEntityManager()->rollback();
    }

    /**
     * @phpstan-return EntityRepository<T>
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->getEntityManager()->getRepository($this->class);
    }
}
