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

use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Types\JsonType;

/**
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

        $this->assertSame(
            '{"foo":"bar"}',
            Type::getType('json')->convertToDatabaseValue(['foo' => 'bar'], $platform)
        );
    }

    public function testConvertToPHPValue(): void
    {
        $platform = new MockPlatform();

        $this->assertSame(
            ['foo' => 'bar'],
            Type::getType('json')->convertToPHPValue('{"foo":"bar"}', $platform)
        );
    }
}

class MockPlatform extends \Doctrine\DBAL\Platforms\AbstractPlatform
{
    /**
     * Gets the SQL Snippet used to declare a BLOB column type.
     */
    public function getBlobTypeDeclarationSQL(array $field): void
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function getBooleanTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function getIntegerTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function getBigIntTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function getSmallIntTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function _getCommonIntegerTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function getVarcharTypeDeclarationSQL(array $field)
    {
        return 'DUMMYVARCHAR()';
    }

    /** @override */
    public function getClobTypeDeclarationSQL(array $field)
    {
        return 'DUMMYCLOB';
    }

    public function getVarcharDefaultLength()
    {
        return 255;
    }

    public function getName()
    {
        return 'mock';
    }

    protected function initializeDoctrineTypeMappings(): void
    {
    }

    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed): void
    {
    }
}
