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

namespace Sonata\Doctrine\Tests\Adapter;

use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Adapter\AdapterChain;
use Sonata\Doctrine\Adapter\AdapterInterface;

final class AdapterChainTest extends TestCase
{
    public function testEmptyAdapter()
    {
        $adapter = new AdapterChain();

        static::assertNull($adapter->getNormalizedIdentifier(new \stdClass()));
        static::assertNull($adapter->getUrlSafeIdentifier(new \stdClass()));
    }

    public function testUrlSafeIdentifier()
    {
        $adapter = new AdapterChain();

        $adapter->addAdapter($fake1 = $this->createMock(AdapterInterface::class));
        $fake1->expects(static::once())->method('getUrlSafeIdentifier')->willReturn(null);

        $adapter->addAdapter($fake2 = $this->createMock(AdapterInterface::class));

        $fake2->expects(static::once())->method('getUrlSafeIdentifier')->willReturn('voila');

        static::assertSame('voila', $adapter->getUrlSafeIdentifier(new \stdClass()));
    }

    public function testNormalizedIdentifier()
    {
        $adapter = new AdapterChain();

        $adapter->addAdapter($fake1 = $this->createMock(AdapterInterface::class));
        $fake1->expects(static::once())->method('getNormalizedIdentifier')->willReturn(null);

        $adapter->addAdapter($fake2 = $this->createMock(AdapterInterface::class));

        $fake2->expects(static::once())->method('getNormalizedIdentifier')->willReturn('voila');

        static::assertSame('voila', $adapter->getNormalizedIdentifier(new \stdClass()));
    }
}
