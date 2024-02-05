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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DiscriminatorColumnMapping;
use Doctrine\Persistence\Event\LoadClassMetadataEventArgs;
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

        $override = OptionsBuilder::createOneToOne('foo', 'bar')
            ->add('fieldName', 'property')
            ->add('length', 100);

        $columnDefinition = ColumnDefinitionBuilder::create()
            ->add('name', 'discriminator')
            ->add('type', 'string');

        $collector = DoctrineCollector::getInstance();

        $collector->addDiscriminator(TestEntity::class, 'test', TestInheritanceEntity::class);
        $collector->addDiscriminatorColumn(TestEntity::class, $columnDefinition);
        $collector->addInheritanceType(TestEntity::class, ClassMetadata::INHERITANCE_TYPE_SINGLE_TABLE);
        $collector->addAssociation(TestEntity::class, 'mapManyToOne', $options);
        $collector->addOverride(TestEntity::class, 'setAttributeOverride', $override);
    }

    /**
     * @psalm-suppress InternalMethod
     *
     * @see https://github.com/symfony/symfony/issues/46483
     */
    public function testDoctrineMappingLoaded(): void
    {
        self::bootKernel();

        $mapper = static::getContainer()->get('sonata.doctrine.mapper');

        static::assertInstanceOf(DoctrineORMMapper::class, $mapper);
    }

    public function testLoadClassMetadata(): void
    {
        self::bootKernel();

        /** @var DoctrineORMMapper $mapper */
        $mapper = static::getContainer()->get('sonata.doctrine.mapper');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');

        $classMetadata = $entityManager->getClassMetadata(TestEntity::class);
        $classMetadata->setDiscriminatorColumn([
            'name' => 'discriminator',
            'length' => 10,
        ]);

        $mapper->loadClassMetadata(new LoadClassMetadataEventArgs(
            $classMetadata,
            static::getContainer()->get('doctrine.orm.entity_manager')
        ));

        $expectedDefinition = [
            'type' => 'string',
            'fieldName' => 'discriminator',
            'name' => 'discriminator',
            'length' => 10,
        ];

        self::assertEquals(
            $classMetadata->discriminatorColumn instanceof DiscriminatorColumnMapping
                ? DiscriminatorColumnMapping::fromMappingArray($expectedDefinition)
                : $expectedDefinition,
            $classMetadata->discriminatorColumn
        );
    }

    /**
     * @return class-string
     */
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
