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

/**
 * @phpstan-extends BaseManager<object>
 */
class ManagerTest extends BaseManager
{
    public function getConnection(): Connection
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function publicCheckObject(object $object): void
    {
        $this->checkObject($object);
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

    /**
     * @var ManagerTest
     */
    private $manager;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManager::class);

        $managerRegistry = $this->createStub(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($this->objectManager);

        $this->manager = new ManagerTest(\stdClass::class, $managerRegistry);
    }

    public function testCheckObject(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Object must be instance of stdClass, DateTime given');

        $this->manager->publicCheckObject(new \DateTime());
    }

    public function testClearManager(): void
    {
        $this->objectManager->expects(static::once())->method('clear');

        $this->manager->clear();
    }
}
