<?php
/**
 * Created by PhpStorm.
 * User: Jahodal
 * Date: 12.9.14
 * Time: 20:54
 */

namespace W3build\PaginateBundle;

class Result implements \Iterator, \Countable {

    private $currentPage;

    private $totalResults;

    private $totalPages;

    private $results;

    private $index = 0;

    public function setTotalResults($totalResults){
        $this->totalResults = $totalResults;

        return $this;
    }

    public function getTotalResults(){
        return $this->totalResults;
    }

    public function setTotalPages($totalPages){
        $this->totalPages = $totalPages;

        return $this;
    }

    public function getTotalPages(){
        return $this->totalPages;
    }

    public function setCurrentPage($currentPage){
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getCurrentPage(){
        return $this->currentPage;
    }

    public function setResults(array $results){
        $this->results = $results;

        return $this;
    }

    public function getResults(){
        return $this->results;
    }



    public function getPagesInRange($pagesInRange = 5){
        $response = new \StdClass();
        $response->showStart = false;
        $response->showEnd = false;
        $response->start = 1;
        $response->end = $this->getTotalPages();

        $offset = ceil($pagesInRange / 2);

        if($pagesInRange < $this->getTotalPages()){
            if(($this->getCurrentPage() + $offset) >= $this->getTotalPages()){
                $response->showStart = true;
                $response->end = $this->getTotalPages();
                $response->start = $this->getTotalPages() - $pagesInRange;
            }
            elseif(($this->getCurrentPage() - $offset) <= 1){
                $response->showEnd = true;
                $response->end = 1 + $pagesInRange;
                $response->start = 1;
            }
            else {
                $response->showStart = true;
                $response->showEnd = true;
                $response->start = $this->getCurrentPage() - $offset;
                $response->end = $this->getCurrentPage() + $offset;
            }
        }

        return $response;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->results[$this->index];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return array_key_exists($this->index, $this->results);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->results);
    }


    public function __toString(){
        return $this->render();
    }
}