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
     * Make sure the code is compatible with legacy code.
     *
     * NEXT_MAJOR: Remove the magic getter.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ('em' === $name) {
            @trigger_error(
                'Accessing to the entity manager through the magic getter is deprecated since'
                .' sonata-project/sonata-doctrine-extensions 1.15 and will throw an exception in 2.0.'
                .' Use the "getObjectManager()" method instead.',
                \E_USER_DEPRECATED
            );

            return $this->getObjectManager();
        }

        throw new \RuntimeException(sprintf('The property %s does not exists', $name));
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/sonata-doctrine-extensions 1.15
     */
    public function getConnection()
    {
        @trigger_error(sprintf(
            'The "%s()" method is deprecated since sonata-project/sonata-doctrine-extensions 1.15'
            .' and will be removed in version 2.0. Use "%s" instead.',
            __METHOD__,
            'getEntityManager()->getConnection()'
        ), \E_USER_DEPRECATED);

        return $this->getEntityManager()->getConnection();
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        $objectManager = $this->getObjectManager();
        \assert($objectManager instanceof EntityManagerInterface);

        return $objectManager;
    }

    /**
     * @phpstan-return EntityRepository<T>
     */
    protected function getRepository(): EntityRepository
    {
        return $this->getEntityManager()->getRepository($this->class);
    }
}

class_exists(\Sonata\CoreBundle\Model\BaseEntityManager::class);
