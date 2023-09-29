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

namespace Sonata\Doctrine\Tests\Adapter\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Adapter\ORM\DoctrineORMAdapter;

final class DoctrineORMAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(UnitOfWork::class)) {
            static::markTestSkipped('Doctrine ORM not installed');
        }
    }

    public function testNormalizedIdentifierWithNoManager(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::once())->method('getManagerForClass')->willReturn(null);

        $adapter = new DoctrineORMAdapter($registry);

        static::assertNull($adapter->getNormalizedIdentifier(new \stdClass()));
    }

    public function testNormalizedIdentifierWithNotManaged(): void
    {
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $unitOfWork->expects(static::once())->method('isInIdentityMap')->willReturn(false);

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->method('getUnitOfWork')->willReturn($unitOfWork);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::once())->method('getManagerForClass')->willReturn($manager);

        $adapter = new DoctrineORMAdapter($registry);

        static::assertNull($adapter->getNormalizedIdentifier(new \stdClass()));
    }

    /**
     * @param int[] $data
     *
     * @dataProvider provideNormalizedIdentifierWithValidObjectCases
     */
    public function testNormalizedIdentifierWithValidObject(array $data, string $expected): void
    {
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $unitOfWork->expects(static::once())->method('isInIdentityMap')->willReturn(true);
        $unitOfWork->expects(static::once())->method('getEntityIdentifier')->willReturn($data);

        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->method('getUnitOfWork')->willReturn($unitOfWork);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::once())->method('getManagerForClass')->willReturn($manager);

        $adapter = new DoctrineORMAdapter($registry);

        static::assertSame($expected, $adapter->getNormalizedIdentifier(new \stdClass()));
    }

    /**
     * @return iterable<array-key, array{array<int>, string}>
     */
    public function provideNormalizedIdentifierWithValidObjectCases(): iterable
    {
        yield [[1], '1'];
        yield [[1, 2], '1~2'];
    }
}
