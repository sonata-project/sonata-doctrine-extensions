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

namespace Sonata\Doctrine\Tests\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Entity\BaseEntityManager;

final class BaseEntityManagerTest extends TestCase
{
    /**
     * @var ManagerRegistry|MockObject
     */
    private $registry;

    /**
     * @var ObjectManager|MockObject
     */
    private $objectManager;

    /**
     * @var BaseEntityManager
     */
    private $manager;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->manager = $this->getMockForAbstractClass(BaseEntityManager::class, ['classname', $this->registry]);
    }

    public function testGetClassName(): void
    {
        $this->assertSame('classname', $this->manager->getClass());
    }

    public function testException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The property exception does not exists');

        $this->manager->exception;
    }

    public function testExceptionOnNonMappedEntity(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to find the mapping information for the class classname. Please check the `auto_mapping` option (http://symfony.com/doc/current/reference/configuration/doctrine.html#configuration-overview) or add the bundle to the `mappings` section in the doctrine configuration');

        $this->registry->expects($this->once())->method('getManagerForClass')->willReturn(null);

        $this->manager->getObjectManager();
    }

    public function testGetEntityManager(): void
    {
        $this->registry->expects($this->once())->method('getManagerForClass')->willReturn($this->objectManager);

        $this->manager->em;
    }

    public function testGetRepository(): void
    {
        $entityRepository = $this->createMock(EntityRepository::class);

        $this->objectManager->expects($this->once())->method('getRepository')->with('classname')->willReturn($entityRepository);

        $this->registry->expects($this->once())->method('getManagerForClass')->willReturn($this->objectManager);

        $r = new \ReflectionObject($this->manager);
        $m = $r->getMethod('getRepository');
        $m->setAccessible(true);

        $this->assertInstanceOf(EntityRepository::class, $m->invoke($this->manager));
    }
}
