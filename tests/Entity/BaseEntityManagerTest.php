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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Entity\BaseEntityManager;

final class BaseEntityManagerTest extends TestCase
{
    /**
     * @var ManagerRegistry&MockObject
     */
    private $registry;

    /**
     * @var ObjectManager&MockObject
     */
    private $objectManager;

    /**
     * @var BaseEntityManager<object>&MockObject
     */
    private $manager;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->objectManager = $this->createMock(EntityManagerInterface::class);
        $this->manager = $this->getMockForAbstractClass(BaseEntityManager::class, ['classname', $this->registry]);
    }

    public function testGetClassName(): void
    {
        static::assertSame('classname', $this->manager->getClass());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     */
    public function testException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The property exception does not exists');

        // @phpstan-ignore-next-line
        $this->manager->exception;
    }

    public function testExceptionOnNonMappedEntity(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to find the mapping information for the class classname. Please check the `auto_mapping` option (http://symfony.com/doc/current/reference/configuration/doctrine.html#configuration-overview) or add the bundle to the `mappings` section in the doctrine configuration');

        $this->registry->expects(static::once())->method('getManagerForClass')->willReturn(null);

        $this->manager->getObjectManager();
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetEntityManager(): void
    {
        $this->registry->expects(static::once())->method('getManagerForClass')->willReturn($this->objectManager);

        // @phpstan-ignore-next-line
        $this->manager->em;
    }

    public function testGetRepository(): void
    {
        $entityRepository = $this->createMock(EntityRepository::class);

        $this->objectManager->expects(static::once())->method('getRepository')->with('classname')->willReturn($entityRepository);

        $this->registry->expects(static::once())->method('getManagerForClass')->willReturn($this->objectManager);

        $r = new \ReflectionObject($this->manager);
        $m = $r->getMethod('getRepository');
        $m->setAccessible(true);

        static::assertInstanceOf(EntityRepository::class, $m->invoke($this->manager));
    }
}
