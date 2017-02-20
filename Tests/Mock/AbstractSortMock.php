<?php

namespace Ip\SorterBundle\Tests\Mock;

use Ip\SorterBundle\Model\AbstractSort;

class AbstractSortMock extends AbstractSort
{
    public $sort;
    public $superCategories = array();

    /**
     * @return integer
     */
    public function getId()
    {
        return -1;
    }

    /**
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param integer $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return array
     */
    public function hasSuperCategory() 
    {
        return $this->superCategories;
    }
}