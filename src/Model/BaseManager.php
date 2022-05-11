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

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @phpstan-template T of object
 * @phpstan-implements ManagerInterface<T>
 */
abstract class BaseManager implements ManagerInterface, ClearableManagerInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var string
     *
     * @phpstan-var class-string<T>
     */
    protected $class;

    /**
     * @param string $class
     *
     * @phpstan-param class-string<T> $class
     */
    public function __construct($class, ManagerRegistry $registry)
    {
        $this->registry = $registry;
        $this->class = $class;
    }

    /**
     * @throws \RuntimeException
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        $manager = $this->registry->getManagerForClass($this->class);

        if (null === $manager) {
            throw new \RuntimeException(sprintf(
                'Unable to find the mapping information for the class %s.'
                .' Please check the `auto_mapping` option'
                .' (http://symfony.com/doc/current/reference/configuration/doctrine.html#configuration-overview)'
                .' or add the bundle to the `mappings` section in the doctrine configuration.',
                $this->class
            ));
        }

        return $manager;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        if (null !== $orderBy) {
            @trigger_error(
                'The $orderBy argument of '.__METHOD__.' is deprecated since sonata-project/doctrine-extensions 1.4, to be removed in 2.0.',
                \E_USER_DEPRECATED
            );
        }

        return $this->getRepository()->findOneBy($criteria);
    }

    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    public function create()
    {
        return new $this->class();
    }

    public function save($entity, $andFlush = true)
    {
        $this->checkObject($entity);

        $this->getObjectManager()->persist($entity);

        if ($andFlush) {
            $this->getObjectManager()->flush();
        }
    }

    public function delete($entity, $andFlush = true)
    {
        $this->checkObject($entity);

        $this->getObjectManager()->remove($entity);

        if ($andFlush) {
            $this->getObjectManager()->flush();
        }
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/sonata-doctrine-extensions 1.15
     */
    public function getTableName()
    {
        @trigger_error(sprintf(
            'The "%s()" method is deprecated since sonata-project/sonata-doctrine-extensions 1.15'
            .' and will be removed in version 2.0.',
            __METHOD__
        ), \E_USER_DEPRECATED);

        $metadata = $this->getObjectManager()->getClassMetadata($this->class);
        \assert($metadata instanceof ClassMetadataInfo);

        return $metadata->table['name'];
    }

    /**
     * NEXT_MAJOR: Remove $objectName parameter and argument along with psalm and phpstan suppressions.
     *
     * @psalm-suppress TooManyArguments
     */
    public function clear(?string $objectName = null): void
    {
        if (\func_num_args() > 0) {
            @trigger_error(sprintf(
                'Passing an argument to "%s()" method is deprecated since sonata-project/sonata-doctrine-extensions 1.17.',
                __METHOD__
            ), \E_USER_DEPRECATED);
        }

        // @phpstan-ignore-next-line
        $this->getObjectManager()->clear($objectName);
    }

    /**
     * Returns the related Object Repository.
     *
     * @return ObjectRepository<object>
     *
     * @phpstan-return ObjectRepository<T>
     */
    protected function getRepository()
    {
        return $this->getObjectManager()->getRepository($this->class);
    }

    /**
     * @param object $object
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     *
     * @phpstan-param T $object
     */
    protected function checkObject($object)
    {
        if (!$object instanceof $this->class) {
            throw new \InvalidArgumentException(sprintf(
                'Object must be instance of %s, %s given',
                $this->class,
                \is_object($object) ? \get_class($object) : \gettype($object)
            ));
        }
    }
}

class_exists(\Sonata\CoreBundle\Model\BaseManager::class);
