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

namespace Sonata\Doctrine\Tests\Mapper\ORM;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\Doctrine\Mapper\Builder\ColumnDefinitionBuilder;
use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Sonata\Doctrine\Mapper\ORM\DoctrineORMMapper;
use Sonata\Doctrine\Tests\App\Entity\TestEntity;
use Sonata\Doctrine\Tests\App\Entity\TestInheritanceEntity;
use Sonata\Doctrine\Tests\App\Entity\TestRelatedEntity;
use Sonata\Doctrine\Tests\App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineORMMapperTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        $options = OptionsBuilder::createManyToOne('relation', TestRelatedEntity::class)
            ->add('joinColumns', [['referencedColumnName' => 'id']]);

        $override = OptionsBuilder::create()
            ->add('fieldName', 'property')
            ->add('length', 100);

        $columnDefinition = ColumnDefinitionBuilder::create()
            ->add('name', 'discriminator')
            ->add('type', 'string');

        $collector = DoctrineCollector::getInstance();

        $collector->addDiscriminator(TestEntity::class, 'test', TestInheritanceEntity::class);
        $collector->addDiscriminatorColumn(TestEntity::class, $columnDefinition);
        $collector->addInheritanceType(TestEntity::class, ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE);
        $collector->addAssociation(TestEntity::class, 'mapManyToOne', $options);
        $collector->addOverride(TestEntity::class, 'setAttributeOverride', $override);
    }

    public function testDoctrineMappingLoaded(): void
    {
        self::bootKernel();

        $mapper = self::getContainer()->get('sonata.doctrine.mapper');

        static::assertInstanceOf(DoctrineORMMapper::class, $mapper);
    }

    /**
     * @return class-string
     */
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
