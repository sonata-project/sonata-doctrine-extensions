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

use Sonata\DatagridBundle\Pager\PagerInterface;

/**
 * @author Raphaël Benitte <benitteraphael@gmail.com>
 *
 * NEXT_MAJOR: Remove this interface.
 *
 * @deprecated since 1.3, to be removed in 2.0. Use Sonata\DatagridBundle\Pager\PageableInterface instead.
 */
interface PageableManagerInterface
{
    /**
     * @param array<string, mixed>  $criteria
     * @param array<string, string> $sort
     * @param int                   $page
     * @param int                   $limit
     *
     * @return PagerInterface
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = []);
}

interface_exists(\Sonata\CoreBundle\Model\PageableManagerInterface::class);
