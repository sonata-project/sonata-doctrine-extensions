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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sonata\Doctrine\Adapter\ORM\DoctrineORMAdapter;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4 (and add conflict)
    $containerConfigurator->services()

        ->set('sonata.doctrine.adapter.doctrine_orm', DoctrineORMAdapter::class)
            ->args([
                new ReferenceConfigurator('doctrine'),
            ]);
};
