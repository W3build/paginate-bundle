<?php
/**
 * Created by PhpStorm.
 * User: Jahodal
 * Date: 12.9.14
 * Time: 20:57
 */

namespace W3build\PaginateBundle;

use W3build\PaginateBundle\Adapter\AdapterAbstract;

class Paginate {

    private $itemsPerPage = 15;

    private $page = 1;

    /**
     * @var Result
     */
    private $result;

    /**
     * @var Adapter\AdapterAbstract
     */
    private $adapter;

    /**
     * @var bool
     */
    private $executed = false;

    /**
     * @param Result $result
     */
    public function __construct(Result $result){
        $this->result = $result;
    }

    public function setItemsPerPage($itemsPerPage){
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    public function setPage($page){
        $this->page = $page;

        return $this;
    }

    /**
     * @param Adapter\AdapterAbstract $adapter
     * @return $this
     */
    public function setAdapter(AdapterAbstract $adapter){
        $this->adapter = $adapter;

        return $this;
    }

    public function getResult(){
        if(!$this->executed){
            $this->executed = true;
            return $this->result = $this->adapter->getResult($this->itemsPerPage, $this->page, $this->result);
        }

        return $this->result;
    }

} 