<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\Doctrine\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class JsonTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Type::addType('json', 'Sonata\Doctrine\Types\JsonType');
    }

    public function testConvertToDatabaseValue()
    {
        $plateform = $this->getMock(' Doctrine\DBAL\Platforms\AbstractPlatform');

        $this->assertEquals(
            '{"foo":"bar"}',
            Type::getType('json')->convertToDatabaseValue(array('foo' => 'bar'), $plateform)
        );
    }

    public function testConvertToPHPValue()
    {
        $plateform = $this->getMock(' Doctrine\DBAL\Platforms\AbstractPlatform');

        $this->assertEquals(
            array('foo' => 'bar'),
            Type::getType('json')->convertToPHPValue('{"foo":"bar"}', $plateform)
        );
    }
}