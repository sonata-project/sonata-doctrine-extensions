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

    /**
     * @dataProvider getWrongEntities
     *
     * @param mixed $entity
     */
    public function testNormalizedIdentifierWithInvalidEntity($entity): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $adapter = new DoctrineORMAdapter($registry);

        $this->expectException(\RuntimeException::class);

        $adapter->getNormalizedIdentifier($entity);
    }

    /**
     * @return iterable<array-key, array{mixed}>
     */
    public function getWrongEntities(): iterable
    {
        yield [0];
        yield [1];
        yield [false];
        yield [true];
        yield [[]];
        yield [''];
        yield ['sonata-project'];
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     *
     * @psalm-suppress NullArgument
     */
    public function testNormalizedIdentifierWithNull(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $adapter = new DoctrineORMAdapter($registry);

        // @phpstan-ignore-next-line
        static::assertNull($adapter->getNormalizedIdentifier(null));
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
        $unitOfWork = $this->getMockBuilder(UnitOfWork::class)->disableOriginalConstructor()->getMock();
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
     * @dataProvider getFixtures
     */
    public function testNormalizedIdentifierWithValidObject(array $data, string $expected): void
    {
        $unitOfWork = $this->getMockBuilder(UnitOfWork::class)->disableOriginalConstructor()->getMock();
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
    public function getFixtures(): iterable
    {
        return [
            [[1], '1'],
            [[1, 2], '1~2'],
        ];
    }
}
