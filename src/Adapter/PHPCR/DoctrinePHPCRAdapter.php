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

namespace Sonata\Doctrine\Adapter\PHPCR;

use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\Doctrine\Adapter\AdapterInterface;

@trigger_error(
    'The Sonata\Doctrine\Adapter\PHPCR\DoctrinePHPCRAdapter class is deprecated'
    .' since sonata-project/doctrine-extensions 1.17, to be removed in version 2.0.',
    \E_USER_DEPRECATED
);

/**
 * NEXT_MAJOR: Remove this class.
 *
 * @deprecated since 1.17 to be remove in 2.0.
 */
class DoctrinePHPCRAdapter implements AdapterInterface
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

        if (!$manager instanceof DocumentManager) {
            return null;
        }

        if (!$manager->contains($model)) {
            return null;
        }

        $class = $manager->getClassMetadata(\get_class($model));

        \assert($class instanceof ClassMetadata);

        return $class->getIdentifierValue($model);
    }

    /**
     * Currently only the leading slash is removed.
     *
     * TODO: do we also have to encode certain characters like spaces or does that happen automatically?
     *
     * {@inheritdoc}
     */
    public function getUrlSafeIdentifier($model)
    {
        $id = $this->getNormalizedIdentifier($model);

        if (null !== $id) {
            return substr($id, 1);
        }

        return null;
    }
}

class_exists(\Sonata\CoreBundle\Model\Adapter\DoctrinePHPCRAdapter::class);
