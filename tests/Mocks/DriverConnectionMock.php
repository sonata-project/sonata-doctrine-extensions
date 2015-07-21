<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace  Sonata\Doctrine\tests\Mocks;

use Doctrine\DBAL\Driver\Connection;

/**
 * Mock class for DriverConnection.
 */
class DriverConnectionMock implements Connection
{
    /**
     * {@inheritdoc}
     */
    public function prepare($prepareString)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        return new StatementMock();
    }

    /**
     * {@inheritdoc}
     */
    public function quote($input, $type = \PDO::PARAM_STR)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function exec($statement)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
    }
}
