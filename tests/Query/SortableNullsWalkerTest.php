<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Doctrine\tests\Query;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Sonata\Doctrine\Query\SortableNullsWalker;

class SortableNullsWalkerTest extends \PHPUnit_Framework_TestCase
{
    protected function getTestEntityManager(AbstractPlatform $platform)
    {
        $config = new Configuration();
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setQueryCacheImpl(new ArrayCache());
        $config->setProxyDir(__DIR__.'/Proxies');
        $config->setProxyNamespace('DoctrineExtensions\Tests\Proxies');
        $config->setAutoGenerateProxyClasses(true);
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(__DIR__.'/../Entities'));

        $conn = array(
            'driverClass' => 'Sonata\Doctrine\Tests\Mocks\DriverMock',
            'platform'    => $platform,
        );
        $entityManager = EntityManager::create($conn, $config);

        return $entityManager;
    }

    private function cleanAlias($sql)
    {
        return preg_replace("/( AS [\w_\d]+)/i", '', $sql);
    }

    private function createSql($entity, AbstractPlatform $platform, $sortDirective = SortableNullsWalker::NULLS_FIRST)
    {
        $dql = "SELECT p FROM {$entity} p ORDER BY p.position ASC";
        $q = $this->getTestEntityManager($platform)->createQuery($dql)
        ->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Sonata\Doctrine\Query\SortableNullsWalker')
        ->setHint('sortableNulls.fields', array('p.position' => $sortDirective))
        ->useQueryCache(false)
        ;

        return self::cleanAlias($q->getSql());
    }

    public function testWalkOrderByItemNullsFirst()
    {
        $entity = 'Sonata\Doctrine\Tests\Entities\BlogPost';

        // MySql
        $sqlToBeConfirmed = 'SELECT b0_.id, b0_.title, b0_.position FROM BlogPost b0_ ORDER BY b0_.position ASC';
        $sqlGenerated = self::createSql($entity, new MySqlPlatform(), SortableNullsWalker::NULLS_FIRST);

        $this->assertEquals(
            $sqlToBeConfirmed,
            $sqlGenerated,
            sprintf('"%s" is not equal of "%s"', $sqlGenerated, $sqlToBeConfirmed)
        );

        // Postgresql
        $sqlToBeConfirmed = 'SELECT b0_.id, b0_.title, b0_.position FROM BlogPost b0_ ORDER BY b0_.position ASC NULLS FIRST';
        $sqlGenerated = self::createSql($entity, new PostgreSqlPlatform(), SortableNullsWalker::NULLS_FIRST);

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
        $sqlToBeConfirmed = 'SELECT b0_.id, b0_.title, b0_.position FROM BlogPost b0_ ORDER BY -b0_.position DESC';
        $sqlGenerated = self::createSql($entity, new MySqlPlatform(), SortableNullsWalker::NULLS_LAST);

        $this->assertEquals(
            $sqlToBeConfirmed,
            $sqlGenerated,
            sprintf('"%s" is not equal of "%s"', $sqlGenerated, $sqlToBeConfirmed)
        );

        // Postgresql
        $sqlToBeConfirmed = 'SELECT b0_.id, b0_.title, b0_.position FROM BlogPost b0_ ORDER BY b0_.position ASC NULLS LAST';
        $sqlGenerated = self::createSql($entity, new PostgreSqlPlatform(), SortableNullsWalker::NULLS_LAST);

        $this->assertEquals(
            $sqlToBeConfirmed,
            $sqlGenerated,
            sprintf('"%s" is not equal of "%s"', $sqlGenerated, $sqlToBeConfirmed)
        );
    }
}
