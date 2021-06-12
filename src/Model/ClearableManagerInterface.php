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

namespace Sonata\Doctrine\Model;

/**
 * @author Jordi Sala <jordism91@gmail.com>
 *
 * @phpstan-template T of object
 */
interface ClearableManagerInterface
{
    /**
     * Clears the object manager.
     */
    public function clear(?string $objectName = null): void;
}
