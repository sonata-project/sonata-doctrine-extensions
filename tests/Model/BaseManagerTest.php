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

namespace Sonata\Doctrine\Tests\Model;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Model\BaseManager;

class ManagerTest extends BaseManager
{
    /**
     * Get the DB driver connection.
     *
     * @return Connection
     */
    public function getConnection()
    {
    }

    /**
     * @param $object
     */
    public function publicCheckObject($object)
    {
        return $this->checkObject($object);
    }
}

/**
 * @author Hugo Briand <briand@ekino.com>
 */
final class BaseManagerTest extends TestCase
{
    public function testCheckObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Object must be instance of class, DateTime given');

        $manager = new ManagerTest('class', $this->createMock(ManagerRegistry::class));

        $manager->publicCheckObject(new \DateTime());
    }
}
