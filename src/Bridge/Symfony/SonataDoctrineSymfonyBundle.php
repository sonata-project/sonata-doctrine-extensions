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

namespace Sonata\Doctrine\Bridge\Symfony;

@trigger_error(sprintf(
    'The %s\SonataDoctrineSymfonyBundle class is deprecated since sonata-project/doctrine-extensions 1.17, to be removed in version 2.0. Use %s instead.',
    __NAMESPACE__,
    SonataDoctrineBundle::class
), \E_USER_DEPRECATED);

class_alias(SonataDoctrineBundle::class, __NAMESPACE__.'\SonataDoctrineSymfonyBundle');
