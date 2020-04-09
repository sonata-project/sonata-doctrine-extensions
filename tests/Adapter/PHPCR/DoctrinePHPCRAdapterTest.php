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

class MyDocument
{
    public $path;
}

final class DoctrinePHPCRAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists(UnitOfWork::class)) {
            $this->markTestSkipped('Doctrine PHPCR not installed');
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

    public function getWrongDocuments(): iterable
    {
        yield [0];
        yield [1];
        yield [false];
        yield [true];
        yield [[]];
    }

    public function testNormalizedIdentifierWithNull()
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $adapter = new DoctrinePHPCRAdapter($registry);

        $this->assertNull($adapter->getNormalizedIdentifier(null));
    }

    public function testNormalizedIdentifierWithNoManager()
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->once())->method('getManagerForClass')->willReturn(null);

        $adapter = new DoctrinePHPCRAdapter($registry);

        $this->assertNull($adapter->getNormalizedIdentifier(new \stdClass()));
    }

    public function testNormalizedIdentifierWithNotManaged()
    {
        $manager = $this->getMockBuilder(DocumentManager::class)->disableOriginalConstructor()->getMock();
        $manager->expects($this->once())->method('contains')->willReturn(false);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->once())->method('getManagerForClass')->willReturn($manager);

        $adapter = new DoctrinePHPCRAdapter($registry);

        $this->assertNull($adapter->getNormalizedIdentifier(new \stdClass()));
    }

    /**
     * @dataProvider getFixtures
     */
    public function testNormalizedIdentifierWithValidObject($data, $expected)
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

        $this->assertSame($data, $adapter->getNormalizedIdentifier($instance));
        $this->assertSame($expected, $adapter->getUrlSafeIdentifier($instance));
    }

    public static function getFixtures()
    {
        return [
            ['/salut', 'salut'],
            ['/les-gens', 'les-gens'],
        ];
    }
}
