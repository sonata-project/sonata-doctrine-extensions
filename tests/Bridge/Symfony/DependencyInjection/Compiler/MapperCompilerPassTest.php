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

namespace Sonata\Doctrine\Tests\Bridge\Symfony\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sonata\Doctrine\Bridge\Symfony\DependencyInjection\Compiler\MapperCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Ahmet Akbana <ahmetakbana@gmail.com>
 */
final class MapperCompilerPassTest extends AbstractCompilerPassTestCase
{
    public function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MapperCompilerPass());
    }

    public function testDefinitionsRemoved()
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('sonata.doctrine.mapper');
    }

    public function testDefinitionsRemovedWithMapper()
    {
        $this->registerService('sonata.doctrine.mapper', 'foo');

        $this->compile();

        $this->assertContainerBuilderNotHasService('sonata.doctrine.mapper');
    }

    public function testDefinitionsRemovedWithDoctrine()
    {
        $this->registerService('doctrine', 'foo');

        $this->compile();

        $this->assertContainerBuilderNotHasService('sonata.doctrine.mapper');
    }

    public function testDefinitionsNotRemoved()
    {
        $this->registerService('sonata.doctrine.mapper', 'foo');
        $this->registerService('doctrine', 'foo');

        $this->compile();

        $this->assertContainerBuilderHasService('sonata.doctrine.mapper');
    }
}
