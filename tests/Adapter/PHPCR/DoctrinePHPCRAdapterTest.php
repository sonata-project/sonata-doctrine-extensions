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

namespace Sonata\Doctrine\Tests\Adapter\PHPCR;

use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Mapping\ClassMetadata;
use Doctrine\ODM\PHPCR\UnitOfWork;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Adapter\PHPCR\DoctrinePHPCRAdapter;

final class MyDocument
{
    /**
     * @var mixed
     */
    public $path;
}

/**
 * NEXT_MAJOR: Remove this test.
 *
 * @group legacy
 *
 * @psalm-suppress UndefinedClass
 */
final class DoctrinePHPCRAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(UnitOfWork::class)) {
            static::markTestSkipped('Doctrine PHPCR not installed');
        }
    }

    /**
     * @dataProvider getWrongDocuments
     *
     * @param mixed $document
     */
    public function testNormalizedIdentifierWithInvalidDocument($document): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $adapter = new DoctrinePHPCRAdapter($registry);

        $this->expectException(\RuntimeException::class);

        $adapter->getNormalizedIdentifier($document);
    }

    /**
     * @return iterable<array-key, array{mixed}>
     */
    public function getWrongDocuments(): iterable
    {
        yield [0];
        yield [1];
        yield [false];
        yield [true];
        yield [[]];
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
        $adapter = new DoctrinePHPCRAdapter($registry);

        // @phpstan-ignore-next-line
        static::assertNull($adapter->getNormalizedIdentifier(null));
    }

    public function testNormalizedIdentifierWithNoManager(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::once())->method('getManagerForClass')->willReturn(null);

        $adapter = new DoctrinePHPCRAdapter($registry);

        static::assertNull($adapter->getNormalizedIdentifier(new \stdClass()));
    }

    public function testNormalizedIdentifierWithNotManaged(): void
    {
        $manager = $this->getMockBuilder(DocumentManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects(static::once())->method('contains')->willReturn(false);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::once())->method('getManagerForClass')->willReturn($manager);

        $adapter = new DoctrinePHPCRAdapter($registry);

        static::assertNull($adapter->getNormalizedIdentifier(new \stdClass()));
    }

    /**
     * @dataProvider getFixtures
     */
    public function testNormalizedIdentifierWithValidObject(string $data, string $expected): void
    {
        $metadata = new ClassMetadata(MyDocument::class);
        $metadata->identifier = 'path';
        $metadata->reflFields['path'] = new \ReflectionProperty(MyDocument::class, 'path');

        $manager = $this->getMockBuilder(DocumentManager::class)->disableOriginalConstructor()->getMock();
        $manager->method('contains')->willReturn(true);
        $manager->method('getClassMetadata')->willReturn($metadata);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($manager);

        $adapter = new DoctrinePHPCRAdapter($registry);

        $instance = new MyDocument();
        $instance->path = $data;

        static::assertSame($data, $adapter->getNormalizedIdentifier($instance));
        static::assertSame($expected, $adapter->getUrlSafeIdentifier($instance));
    }

    /**
     * @return iterable<array-key, array{string, string}>
     */
    public static function getFixtures(): iterable
    {
        return [
            ['/salut', 'salut'],
            ['/les-gens', 'les-gens'],
        ];
    }
}
