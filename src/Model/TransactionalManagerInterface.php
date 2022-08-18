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

use Sonata\Doctrine\Exception\TransactionException;

/**
 * @author Erison Silva <erison.sdn@gmail.com>
 */
interface TransactionalManagerInterface
{
    public function beginTransaction(): void;

    /**
     * @throws TransactionException
     */
    public function commit(): void;

    public function rollBack(): void;
}
