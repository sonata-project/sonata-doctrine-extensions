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

namespace Sonata\Doctrine\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\Doctrine\Tests\App\Entity\TestEntity;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new DoctrineBundle(),
            new FrameworkBundle(),
            new SonataDoctrineBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir().'cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir().'log';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'http_method_override' => true,
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite://:memory:'],
            'orm' => [
                'report_fields_where_declared' => true,
                'mappings' => [
                    'Entity' => [
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/Entity',
                        'prefix' => TestEntity::class,
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir().'/sonata-doctrine-extensions/var/';
    }
}
