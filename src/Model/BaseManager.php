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

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @phpstan-template T of object
 * @phpstan-implements ManagerInterface<T>
 */
abstract class BaseManager implements ManagerInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $class;

    /**
     * @phpstan-param class-string<T> $class
     */
    public function __construct(string $class, ManagerRegistry $registry)
    {
        $this->registry = $registry;
        $this->class = $class;
    }

    /**
     * @throws \RuntimeException
     */
    public function getObjectManager(): ObjectManager
    {
        $manager = $this->registry->getManagerForClass($this->class);

        if (!$manager) {
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

    public function getClass(): string
    {
        return $this->class;
    }

    public function findAll(): array
    {
        return $this->getRepository()->findAll();
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        if (null !== $orderBy) {
            @trigger_error(
                'The $orderBy argument of '.__METHOD__.' is deprecated since sonata-project/doctrine-extensions 1.4, to be removed in 2.0.',
                E_USER_DEPRECATED
            );
        }

        return $this->getRepository()->findOneBy($criteria);
    }

    public function find($id): ?object
    {
        return $this->getRepository()->find($id);
    }

    public function create(): object
    {
        return new $this->class();
    }

    public function save(object $entity, bool $andFlush = true): void
    {
        $this->checkObject($entity);

        $this->getObjectManager()->persist($entity);

        if ($andFlush) {
            $this->getObjectManager()->flush();
        }
    }

    public function delete(object $entity, bool $andFlush = true): void
    {
        $this->checkObject($entity);

        $this->getObjectManager()->remove($entity);

        if ($andFlush) {
            $this->getObjectManager()->flush();
        }
    }

    public function getTableName(): string
    {
        return $this->getObjectManager()->getClassMetadata($this->class)->table['name'];
    }

    /**
     * Returns the related Object Repository.
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->getObjectManager()->getRepository($this->class);
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function checkObject($object): void
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
