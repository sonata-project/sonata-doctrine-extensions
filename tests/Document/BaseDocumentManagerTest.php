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

namespace Sonata\Doctrine\Tests\Document;

use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Document\BaseDocumentManager;

class DocumentManager extends BaseDocumentManager
{
}

final class BaseDocumentManagerTest extends TestCase
{
    public function getManager()
    {
        $registry = $this->createMock(ManagerRegistry::class);

        return new DocumentManager('classname', $registry);
    }

    public function test()
    {
        $this->assertSame('classname', $this->getManager()->getClass());
    }
}
