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
use Doctrine\Persistence\ObjectManager;
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
     * @return DocumentManager
     */
    public function getDocumentManager(): ObjectManager
    {
        $dm = $this->getObjectManager();

        \assert($dm instanceof DocumentManager);

        return $dm;
    }
}
