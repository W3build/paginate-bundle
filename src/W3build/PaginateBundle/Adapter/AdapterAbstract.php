<?php
/**
 * Created by PhpStorm.
 * User: Jahodal
 * Date: 12.9.14
 * Time: 20:53
 */

namespace W3build\PaginateBundle\Adapter;


use W3build\PaginateBundle\Result;

abstract class AdapterAbstract {

    public abstract function getResult($itemsPerPage, $page, Result $result);

    public function populateResult(Result $result, $totalResults, $totalPages, $currentPage, array $results){
        $result->setTotalResults($totalResults)
               ->setTotalPages($totalPages)
               ->setCurrentPage($currentPage)
               ->setResults($results);

        return $result;
    }

} 