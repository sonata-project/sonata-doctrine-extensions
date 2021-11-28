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

namespace Sonata\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * NEXT_MAJOR: Remove this class.
 *
 * Convert a value into a json string to be stored into the persistency layer.
 *
 * @deprecated since sonata-project/doctrine-extensions 1.2, to be removed in 2.0. Use JsonType from Doctrine DBAL instead.
 */
class JsonType extends Type
{
    public const JSON = 'json';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return json_decode((string) $value, true);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode($value);
    }

    public function getName()
    {
        return self::JSON;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
