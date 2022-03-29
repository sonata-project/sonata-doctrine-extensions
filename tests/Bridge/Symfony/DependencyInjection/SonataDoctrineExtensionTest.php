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

namespace Sonata\Doctrine\Tests\Bridge\Symfony\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\Doctrine\Bridge\Symfony\DependencyInjection\SonataDoctrineExtension;

final class SonataDoctrineExtensionTest extends AbstractExtensionTestCase
{
    public function testServicesAreLoaded(): void
    {
        // simulate DoctrinePHPCRBundle is installed
        $kernelBundles = $this->container->getParameterBag()->has('kernel.bundles') ?
            (array) $this->container->getParameterBag()->get('kernel.bundles') : [];
        $this->container->getParameterBag()->set(
            'kernel.bundles',
            ['DoctrinePHPCRBundle' => true] + $kernelBundles
        );

        $this->load();

        $this->assertContainerBuilderHasService('sonata.doctrine.model.adapter.chain');

        $this->assertContainerBuilderHasService('sonata.doctrine.adapter.doctrine_orm');
        $this->assertContainerBuilderHasService('sonata.doctrine.mapper');

        $this->assertContainerBuilderHasService('sonata.doctrine.adapter.doctrine_phpcr');
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SonataDoctrineExtension(),
        ];
    }
}
