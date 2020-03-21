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

namespace Sonata\Doctrine\Tests\Mapper\Builder;

use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;

final class OptionsBuilderTest extends TestCase
{
    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testOneToOne(): void
    {
        $builder = OptionsBuilder::createOneToOne('field', 'App\Entity\Address');

        $this->assertSame([
            'fieldName' => 'field',
            'targetEntity' => 'App\Entity\Address',
        ], $builder->getOptions());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testCreateManyToOne(): void
    {
        $builder = OptionsBuilder::createManyToOne('address', 'App\Entity\Address');

        $this->assertSame([
            'fieldName' => 'address',
            'targetEntity' => 'App\Entity\Address',
        ], $builder->getOptions());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testCreateOneToMany(): void
    {
        $builder = OptionsBuilder::createOneToMany('features', 'App\Entity\Feature');

        $this->assertSame([
            'fieldName' => 'features',
            'targetEntity' => 'App\Entity\Feature',
        ], $builder->getOptions());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testCreateManyToMany(): void
    {
        $builder = OptionsBuilder::createManyToMany('groups', 'App\Entity\Group');

        $this->assertSame([
            'fieldName' => 'groups',
            'targetEntity' => 'App\Entity\Group',
        ], $builder->getOptions());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testJoinTable(): void
    {
        $builder = OptionsBuilder::createManyToMany('groups', 'App\Entity\Group')
            ->addJoinTable('user_group', [[
                'name' => 'user_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
            ]], [[
                'name' => 'group_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
            ]])
        ;

        $this->assertSame([
            'fieldName' => 'groups',
            'targetEntity' => 'App\Entity\Group',
            'joinTable' => [
                'name' => 'user_group',
                'joinColumns' => [
                    [
                        'name' => 'user_id',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'CASCADE',
                    ],
                ],
                'inverseJoinColumns' => [[
                    'name' => 'group_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ]],
            ],
        ], $builder->getOptions());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testCascade(): void
    {
        $builder = OptionsBuilder::createOneToMany('groups', 'App\Entity\Group')
            ->cascade(['persist', 'refresh']);

        $this->assertSame([
            'fieldName' => 'groups',
            'targetEntity' => 'App\Entity\Group',
            'cascade' => ['persist', 'refresh'],
        ], $builder->getOptions());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testOrphanRemoval(): void
    {
        $builder = OptionsBuilder::createOneToMany('groups', 'App\Entity\Group')
            ->orphanRemoval();

        $this->assertSame([
            'fieldName' => 'groups',
            'targetEntity' => 'App\Entity\Group',
            'orphanRemoval' => true,
        ], $builder->getOptions());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testOrphanRemovalThrowsExceptionOnInvalidMapping(): void
    {
        $this->expectException(\RuntimeException::class);

        OptionsBuilder::createManyToOne('groups', 'App\Entity\Group')
            ->orphanRemoval();
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testAddJoin(): void
    {
        $builder = OptionsBuilder::createOneToOne('groups', 'App\Entity\Group')
            ->addJoin([
                'name' => 'parent_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
            ])
            ->addJoin([
                'name' => 'another_parent_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
            ])
        ;

        $this->assertSame([
            'fieldName' => 'groups',
            'targetEntity' => 'App\Entity\Group',
            'joinColumns' => [[
                'name' => 'parent_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
            ],
                [
                    'name' => 'another_parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',

                ], ],
        ], $builder->getOptions());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testAddJoinThrowsExceptionOnInvalidMapping(): void
    {
        $this->expectException(\RuntimeException::class);

        OptionsBuilder::createOneToMany('groups', 'App\Entity\Group')
            ->addJoin([
                'name' => 'parent_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
            ])
            ->addJoin([
                'name' => 'another_parent_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
            ])
        ;
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testOrderBy(): void
    {
        $builder = OptionsBuilder::createOneToMany('groups', 'App\Entity\Group')
            ->addOrder('position', 'ASC')
            ->addOrder('name', 'DESC')
        ;

        $this->assertSame([
            'fieldName' => 'groups',
            'targetEntity' => 'App\Entity\Group',
            'orderBy' => [
                'position' => 'ASC',
                'name' => 'DESC',
            ],
        ], $builder->getOptions());
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testOrderByThrowsExceptionOnInvalidMapping(): void
    {
        $this->expectException(\RuntimeException::class);

        OptionsBuilder::createOneToOne('groups', 'App\Entity\Group')
            ->addOrder('name', 'DESC')
        ;
    }

    public function testCreate(): void
    {
        $builder = OptionsBuilder::create()
            ->add('foo', 'bar')
            ->add('bar', 'foo')
            ->add('foobar', [
                'foo', 'bar',
            ]);

        $this->assertSame([
            'foo' => 'bar',
            'bar' => 'foo',
            'foobar' => [
                'foo', 'bar',
            ],
        ], $builder->getOptions());
    }
}
