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

namespace Sonata\Doctrine\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use Sonata\Doctrine\Model\BaseManager;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @phpstan-template T of object
 * @phpstan-extends BaseManager<T>
 */
abstract class BaseDocumentManager extends BaseManager
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
        if ('dm' === $name) {
            @trigger_error(
                'Accessing to the document manager through the magic getter is deprecated since'
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
        throw new \LogicException('MongoDB does not use a database connection.');
    }

    /**
     * @return DocumentManager
     */
    public function getDocumentManager()
    {
        $dm = $this->getObjectManager();

        \assert($dm instanceof DocumentManager);

        return $dm;
    }
}

class_exists(\Sonata\CoreBundle\Model\BaseDocumentManager::class);
