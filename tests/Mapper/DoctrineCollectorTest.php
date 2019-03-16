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

namespace Sonata\Doctrine\Tests\Mapper;

use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use stdClass;

class DoctrineCollectorTest extends TestCase
{
    /**
     * @covers \Sonata\Doctrine\Mapper\DoctrineCollector::getIndexes
     * @covers \Sonata\Doctrine\Mapper\DoctrineCollector::getUniques
     * @covers \Sonata\Doctrine\Mapper\DoctrineCollector::getInheritanceTypes
     * @covers \Sonata\Doctrine\Mapper\DoctrineCollector::getDiscriminatorColumns
     * @covers \Sonata\Doctrine\Mapper\DoctrineCollector::getAssociations
     * @covers \Sonata\Doctrine\Mapper\DoctrineCollector::getDiscriminators
     */
    public function testDefaultValues(): void
    {
        $collector = DoctrineCollector::getInstance();
        $this->assertSame([], $collector->getIndexes());
        $this->assertSame([], $collector->getUniques());
        $this->assertSame([], $collector->getInheritanceTypes());
        $this->assertSame([], $collector->getDiscriminatorColumns());
        $this->assertSame([], $collector->getAssociations());
        $this->assertSame([], $collector->getDiscriminators());
        $this->assertSame([], $collector->getOverrides());
    }

    public function testClear(): void
    {
        $collector = DoctrineCollector::getInstance();
        $collector->addIndex(stdClass::class, 'name', ['column']);
        $collector->addUnique(stdClass::class, 'name', ['column']);
        $collector->addInheritanceType(stdClass::class, 'type');
        $collector->addDiscriminatorColumn(stdClass::class, ['columnDef']);
        $collector->addAssociation(stdClass::class, 'type', ['options']);
        $collector->addDiscriminator(stdClass::class, 'key', 'discriminatorClass');
        $collector->addOverride(stdClass::class, 'type', ['options']);

        $collector->clear();

        $this->assertSame([], $collector->getIndexes());
        $this->assertSame([], $collector->getUniques());
        $this->assertSame([], $collector->getInheritanceTypes());
        $this->assertSame([], $collector->getDiscriminatorColumns());
        $this->assertSame([], $collector->getAssociations());
        $this->assertSame([], $collector->getDiscriminators());
        $this->assertSame([], $collector->getOverrides());
    }
}
