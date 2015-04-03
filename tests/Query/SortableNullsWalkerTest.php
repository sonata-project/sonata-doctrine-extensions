<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Doctrine\Tests\Query;

use Doctrine\ORM\Query;
use Sonata\Doctrine\Query\SortableNullsWalker;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Sonata\Doctrine\Tests\Mocks\DriverMock;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;

class SortableNullsWalkerTest extends \PHPUnit_Framework_TestCase
{
    protected function getTestEntityManager(AbstractPlatform $platform)
    {
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $config->setProxyDir(__DIR__.'/Proxies');
        $config->setProxyNamespace('DoctrineExtensions\Tests\Proxies');
        $config->setAutoGenerateProxyClasses(true);
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(__DIR__.'/../Entities'));

        $conn = array(
            'driverClass' => 'Sonata\Doctrine\Tests\Mocks\DriverMock',
            'platform' => $platform,
        );
        $entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);

        return $entityManager;
    }

    public function testWalkOrderByItemNullsFirst()
    {
        $entity = 'Sonata\Doctrine\Tests\Entities\BlogPost';

        // MySql
        $dql = "SELECT p FROM {$entity} p ORDER BY p.position ASC";
        $q = $this->getTestEntityManager(new MySqlPlatform())->createQuery($dql)
                ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Sonata\Doctrine\Query\SortableNullsWalker')
                ->setHint("sortableNulls.fields", array("p.position" => SortableNullsWalker::NULLS_FIRST))
        ;

        $sqlToBeConfirmed = "SELECT b0_.id AS id_0, b0_.title AS title_1, b0_.position AS position_2 FROM BlogPost b0_ ORDER BY b0_.position ASC";

        $sqlGenerated =  $q->getSql();

        $this->assertEquals(
            $sqlToBeConfirmed,
            $sqlGenerated,
            sprintf('"%s" is not equal of "%s"', $sqlGenerated, $sqlToBeConfirmed)
        );

        // Postgresql
        $dql = "SELECT p FROM {$entity} p ORDER BY p.position ASC";
        $q = $this->getTestEntityManager(new PostgreSqlPlatform())->createQuery($dql)
                ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Sonata\Doctrine\Query\SortableNullsWalker')
                ->setHint("sortableNulls.fields", array("p.position" => SortableNullsWalker::NULLS_FIRST))
        ;

        $sqlToBeConfirmed = "SELECT b0_.id AS id_0, b0_.title AS title_1, b0_.position AS position_2 FROM BlogPost b0_ ORDER BY b0_.position ASC NULLS FIRST";

        $sqlGenerated =  $q->getSql();

        $this->assertEquals(
            $sqlToBeConfirmed,
            $sqlGenerated,
            sprintf('"%s" is not equal of "%s"', $sqlGenerated, $sqlToBeConfirmed)
        );
    }

    public function testWalkOrderByItemNullsLast()
    {
        $entity = 'Sonata\Doctrine\Tests\Entities\BlogPost';

        // MySql
        $dql = "SELECT p FROM {$entity} p ORDER BY p.position ASC";
        $q = $this->getTestEntityManager(new MySqlPlatform())->createQuery($dql)
                ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Sonata\Doctrine\Query\SortableNullsWalker')
                ->setHint("sortableNulls.fields", array("p.position" => SortableNullsWalker::NULLS_LAST))
        ;

        $sqlToBeConfirmed = "SELECT b0_.id AS id_0, b0_.title AS title_1, b0_.position AS position_2 FROM BlogPost b0_ ORDER BY b0_.position ASC";

        $sqlGenerated =  $q->getSql();

        $this->assertEquals(
            $sqlToBeConfirmed,
            $sqlGenerated,
            sprintf('"%s" is not equal of "%s"', $sqlGenerated, $sqlToBeConfirmed)
        );

        // Postgresql
        $dql = "SELECT p FROM {$entity} p ORDER BY p.position ASC";
        $q = $this->getTestEntityManager(new PostgreSqlPlatform())->createQuery($dql)
                ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Sonata\Doctrine\Query\SortableNullsWalker')
                ->setHint("sortableNulls.fields", array("p.position" => SortableNullsWalker::NULLS_LAST))
        ;

        $sqlToBeConfirmed = "SELECT b0_.id AS id_0, b0_.title AS title_1, b0_.position AS position_2 FROM BlogPost b0_ ORDER BY b0_.position ASC NULLS LAST";

        $sqlGenerated =  $q->getSql();

        $this->assertEquals(
            $sqlToBeConfirmed,
            $sqlGenerated,
            sprintf('"%s" is not equal of "%s"', $sqlGenerated, $sqlToBeConfirmed)
        );
    }
}
