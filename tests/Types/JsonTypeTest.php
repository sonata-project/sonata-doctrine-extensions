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

namespace Sonata\Doctrine\Types\Tests;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Types\JsonType;

/**
 * NEXT_MAJOR: Remove this test.
 *
 * @group legacy
 */
class JsonTypeTest extends TestCase
{
    protected function setUp(): void
    {
        if (Type::hasType('json')) {
            Type::overrideType('json', JsonType::class);
        } else {
            Type::addType('json', JsonType::class);
        }
    }

    public function testConvertToDatabaseValue(): void
    {
        $platform = new MockPlatform();

        static::assertSame(
            '{"foo":"bar"}',
            Type::getType('json')->convertToDatabaseValue(['foo' => 'bar'], $platform)
        );

        static::assertNull(
            Type::getType('json')->convertToDatabaseValue(null, $platform)
        );
    }

    public function testConvertToPHPValue(): void
    {
        $platform = new MockPlatform();

        static::assertSame(
            ['foo' => 'bar'],
            Type::getType('json')->convertToPHPValue('{"foo":"bar"}', $platform)
        );

        static::assertNull(
            Type::getType('json')->convertToPHPValue(null, $platform)
        );
    }
}

class MockPlatform extends AbstractPlatform
{
    /**
     * Gets the SQL Snippet used to declare a BLOB column type.
     */
    public function getBlobTypeDeclarationSQL(array $column): string
    {
        throw Exception::notSupported(__METHOD__);
    }

    public function getBooleanTypeDeclarationSQL(array $column): string
    {
        return '';
    }

    public function getIntegerTypeDeclarationSQL(array $column): string
    {
        return '';
    }

    public function getBigIntTypeDeclarationSQL(array $column): string
    {
        return '';
    }

    public function getSmallIntTypeDeclarationSQL(array $column): string
    {
        return '';
    }

    public function _getCommonIntegerTypeDeclarationSQL(array $column): string
    {
        return '';
    }

    public function getVarcharTypeDeclarationSQL(array $column): string
    {
        return 'DUMMYVARCHAR()';
    }

    public function getCurrentDatabaseExpression(): string
    {
        return '';
    }

    /** @override */
    public function getClobTypeDeclarationSQL(array $column): string
    {
        return 'DUMMYCLOB';
    }

    public function getVarcharDefaultLength(): int
    {
        return 255;
    }

    public function getName(): string
    {
        return 'mock';
    }

    protected function initializeDoctrineTypeMappings(): void
    {
    }

    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed): string
    {
        return '';
    }
}
