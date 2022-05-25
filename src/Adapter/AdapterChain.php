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

namespace Sonata\Doctrine\Adapter;

final class AdapterChain implements AdapterInterface
{
    /**
     * @var AdapterInterface[]
     */
    private array $adapters = [];

    public function addAdapter(AdapterInterface $adapter): void
    {
        $this->adapters[] = $adapter;
    }

    public function getNormalizedIdentifier(object $model): ?string
    {
        foreach ($this->adapters as $adapter) {
            $identifier = $adapter->getNormalizedIdentifier($model);

            if (null !== $identifier) {
                return $identifier;
            }
        }

        return null;
    }

    public function getUrlSafeIdentifier(object $model): ?string
    {
        foreach ($this->adapters as $adapter) {
            $safeIdentifier = $adapter->getUrlSafeIdentifier($model);

            if (null !== $safeIdentifier) {
                return $safeIdentifier;
            }
        }

        return null;
    }
}
