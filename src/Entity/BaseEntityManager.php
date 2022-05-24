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
use Sonata\Doctrine\Model\BaseManager;

/**
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 *
 * @phpstan-template T of object
 * @phpstan-extends BaseManager<T>
 */
abstract class BaseEntityManager extends BaseManager
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

    /**
     * @phpstan-return EntityRepository<T>
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->getEntityManager()->getRepository($this->class);
    }
}
