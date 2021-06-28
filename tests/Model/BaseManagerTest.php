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
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Model\BaseManager;

class ManagerTest extends BaseManager
{
    /**
     * Get the DB driver connection.
     */
    public function getConnection(): Connection
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
    /**
     * @var MockObject&ObjectManager
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);

        $managerRegistry = $this->createStub(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($this->objectManager);

        $this->manager = new ManagerTest('class', $managerRegistry);
    }

    public function testCheckObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Object must be instance of class, DateTime given');

        $this->manager->publicCheckObject(new \DateTime());
    }

    public function testClearManager()
    {
        $this->objectManager->expects($this->once())->method('clear');

        $this->manager->clear();
    }
}
