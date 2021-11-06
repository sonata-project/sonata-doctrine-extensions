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

namespace Sonata\Doctrine\Adapter\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\Doctrine\Adapter\AdapterInterface;

/**
 * This is a port of the DoctrineORMAdminBundle / ModelManager class.
 */
class DoctrineORMAdapter implements AdapterInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getNormalizedIdentifier($model)
    {
        // NEXT_MAJOR: Remove this check and add type hint instead.
        if (!\is_object($model)) {
            if (null === $model) {
                @trigger_error(sprintf(
                    'Passing other type than object as argument 1 for method %s() is deprecated since'
                    .' sonata-project/doctrine-extensions 1.15. It will accept only object in version 2.0.',
                    __METHOD__
                ), \E_USER_DEPRECATED);

                return null;
            }

            throw new \RuntimeException(sprintf(
                'Argument 1 passed to "%s()" must be an object, %s given.',
                __METHOD__,
                \gettype($model)
            ));
        }

        $manager = $this->registry->getManagerForClass(\get_class($model));

        if (!$manager instanceof EntityManagerInterface) {
            return null;
        }

        if (!$manager->getUnitOfWork()->isInIdentityMap($model)) {
            return null;
        }

        return implode(self::ID_SEPARATOR, $manager->getUnitOfWork()->getEntityIdentifier($model));
    }

    /**
     * {@inheritdoc}
     *
     * The ORM implementation does nothing special but you still should use
     * this method when using the id in a URL to allow for future improvements.
     */
    public function getUrlSafeIdentifier($model)
    {
        return $this->getNormalizedIdentifier($model);
    }
}

class_exists(\Sonata\CoreBundle\Model\Adapter\DoctrineORMAdapter::class);
