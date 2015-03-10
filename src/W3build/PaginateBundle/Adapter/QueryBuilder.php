<?php
/**
 * Created by PhpStorm.
 * User: Jahodal
 * Date: 12.9.14
 * Time: 20:52
 */

namespace W3build\PaginateBundle\Adapter;


use W3build\PaginateBundle\Result;
use Doctrine\ORM as ORM;

class QueryBuilder extends AdapterAbstract {

    private $executed = false;

    /**
     * @var ORM\QueryBuilder
     */
    private $query;

    /**
     * @var ORM\QueryBuilder
     */
    private $numRowsSubQuery;

    private function createNumRowsSubQuery(){
        $parts = $this->query->getDQLParts();
        $from = $parts['from'][0];
        $where = $parts['where'];

        $entityManager = $this->query->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('COUNT(DISTINCT countAlias.id)')
            ->from($from->getFrom(), 'countAlias');

        $aliasCount = 1;
        $aliases = array($from->getAlias() => 'countAlias');

        foreach($parts['join'] as $joinSource => $joins){
            if(!array_key_exists($joinSource, $aliases)){
                $aliases[$joinSource] = 'countAlias' . $aliasCount;
                ++$aliasCount;
            }

            foreach($joins as $join){
                if(!array_key_exists($join->getAlias(), $aliases)){
                    $aliases[$join->getAlias()] = 'countAlias' . $aliasCount;
                    ++$aliasCount;
                }

                preg_match('#[a-zA-Z_]*\.+#', $join->getJoin(), $matches);
                $joinCondition = preg_replace('#[a-zA-Z_]*\.+#', $aliases[substr($matches[0], 0, -1)] . '.', $join->getJoin());

                switch($join->getJoinType()){
                    case \Doctrine\ORM\Query\Expr\Join::INNER_JOIN:
                        $queryBuilder->innerJoin($joinCondition, $aliases[$join->getAlias()]);
                        break;
                    case \Doctrine\ORM\Query\Expr\Join::LEFT_JOIN:
                        $queryBuilder->leftJoin($joinCondition, $aliases[$join->getAlias()]);
                        break;
                    default:
                        throw new \Exception('Unknow join type');
                        break;
                }
            }
        }

        if($where){
            $whereCondition = $where->__toString();
            preg_match_all('#([a-zA-Z_]*)\.+#', $whereCondition, $matches);
            foreach($matches[1] as $alias){
                $whereCondition = preg_replace('#' . $alias . '\.+#', $aliases[$alias] . '.', $whereCondition);
            }
            $queryBuilder->where($whereCondition);
        }

        return $queryBuilder;
    }

    private function getNumRowsSubQuery(){
        if(!$this->numRowsSubQuery){
            $this->numRowsSubQuery = $this->createNumRowsSubQuery();
        }

        return $this->numRowsSubQuery->getDQL();
    }

    public function __construct(ORM\QueryBuilder $query, ORM\QueryBuilder $numRowsSubQuery = null){
        $this->query = $query;
        $this->numRowsSubQuery = $numRowsSubQuery;
    }

    public function getResult($itemsPerPage, $page, Result $result)
    {
        $parts = $this->query->getDQLParts();
        $from = $parts['from'][0];

        $itemsOnPage = clone $this->query;

        $itemsOnPage->select('DISTINCT(' . $from->getAlias() .'.id)')
            ->addSelect('(' . $this->getNumRowsSubQuery() . ') as num_rows')
            ->from($from->getFrom(), 'master')
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($itemsPerPage * ($page - 1));

        $itemsOnPage = $itemsOnPage->getQuery()->getScalarResult();

        $totalResults = 0;
        $ids = array();
        if($itemsOnPage){
            $totalResults = $itemsOnPage[0]['num_rows'];
            foreach($itemsOnPage as $itemOnPage){
                $ids[] = $itemOnPage[1];
            }
        }

        $totalPages = ceil($totalResults / $itemsPerPage);

        $this->query->resetDQLPart('where');
        $this->query->where($from->getAlias() . ' IN (:ids)')
            ->setParameters(array('ids' => $ids));

        $results = $this->query->getQuery()->getResult();

        return $this->populateResult($result, $totalResults, $totalPages, $page, $results);
    }
}