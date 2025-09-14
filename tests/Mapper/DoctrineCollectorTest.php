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

use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Mapper\Builder\ColumnDefinitionBuilder;
use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;

#[CoversMethod(DoctrineCollector::class, 'getIndexes')]
#[CoversMethod(DoctrineCollector::class, 'getUniques')]
#[CoversMethod(DoctrineCollector::class, 'getInheritanceTypes')]
#[CoversMethod(DoctrineCollector::class, 'getDiscriminatorColumns')]
#[CoversMethod(DoctrineCollector::class, 'getAssociations')]
#[CoversMethod(DoctrineCollector::class, 'getDiscriminators')]
final class DoctrineCollectorTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $collector = DoctrineCollector::getInstance();
        static::assertSame([], $collector->getIndexes());
        static::assertSame([], $collector->getUniques());
        static::assertSame([], $collector->getInheritanceTypes());
        static::assertSame([], $collector->getDiscriminatorColumns());
        static::assertSame([], $collector->getAssociations());
        static::assertSame([], $collector->getDiscriminators());
        static::assertSame([], $collector->getOverrides());
    }

    public function testClear(): void
    {
        $collector = DoctrineCollector::getInstance();
        $collector->addIndex(\stdClass::class, 'name', ['column']);
        $collector->addUnique(\stdClass::class, 'name', ['column']);
        $collector->addInheritanceType(\stdClass::class, ClassMetadata::INHERITANCE_TYPE_SINGLE_TABLE);
        $collector->addDiscriminatorColumn(\stdClass::class, ColumnDefinitionBuilder::create()
            ->add('columnDef', ''));
        $collector->addAssociation(\stdClass::class, 'type', OptionsBuilder::createOneToOne('foo', 'bar')
            ->add('foo', 'bar'));
        $collector->addDiscriminator(\stdClass::class, 'key', \stdClass::class);
        $collector->addOverride(\stdClass::class, 'type', OptionsBuilder::createOneToOne('foo', 'bar')
            ->add('foo', 'bar'));

        $collector->clear();

        static::assertSame([], $collector->getIndexes());
        static::assertSame([], $collector->getUniques());
        static::assertSame([], $collector->getInheritanceTypes());
        static::assertSame([], $collector->getDiscriminatorColumns());
        static::assertSame([], $collector->getAssociations());
        static::assertSame([], $collector->getDiscriminators());
        static::assertSame([], $collector->getOverrides());
    }
}
