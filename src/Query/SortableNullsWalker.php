<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Doctrine\Query;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\AST\PathExpression;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @see http://www.doctrine-project.org/jira/browse/DDC-490
 *
 * The SortableNullsWalker is a TreeWalker that walks over a DQL AST and constructs
 * the corresponding SQL to allow ORDER BY x ASC NULLS FIRST|LAST.
 *
 * [use]
 * $qb = $em->createQueryBuilder()
 *			->select('p')
 *			->from('Webges\Domain\Core\Person\Person', 'p')
 *			->where('p.id = 1')
 *			->orderBy('p.firstname', 'ASC')
 *			->addOrderBy('p.lastname', 'DESC')
 *			->addOrderBy('p.basedOnPerson.id', 'DESC'); // relation to person
 *
 * $query = $qb->getQuery();
 * $query->setHint(Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Webges\DoctrineExtensions\Query\SortableNullsWalker');
 * $query->setHint("sortableNulls.fields", array(
 *				"p.firstname" => Webges\DoctrineExtensions\Query\SortableNullsWalker::NULLS_FIRST,
 *				"p.lastname"  => Webges\DoctrineExtensions\Query\SortableNullsWalker::NULLS_LAST,
 *				"p.basedOnPerson.id" => Webges\DoctrineExtensions\Query\SortableNullsWalker::NULLS_LAST
 *			));
 */
class SortableNullsWalker extends Query\SqlWalker
{

    const NULLS_FIRST = 'NULLS FIRST';

    const NULLS_LAST = 'NULLS LAST';

    public function walkOrderByItem($orderByItem)
    {
        $sql = parent::walkOrderByItem($orderByItem);

        $platform = $this->getConnection()->getDatabasePlatform()->getName();

        switch ($platform) {

            case 'postgresql':
            case 'oracle' :
                $hint = $this->getQuery()->getHint('sortableNulls.fields');
                $expr = $orderByItem->expression;
                $type = strtoupper($orderByItem->type);

                if (is_array($hint) && count($hint)) {
                    $search = $this->walkPathExpression($expr) . ' ' . $type;
                    $index = $expr->identificationVariable . '.' . $expr->field;
                    $sql = str_replace($search, $search . ' ' . $hint[$index], $sql);
                }
                break;

            case 'mysql' :
                break;
        }

        return $sql;
    }
}
