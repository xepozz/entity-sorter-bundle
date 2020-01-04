<?php

namespace Xepozz\EntitySorterBundle\Tests\Mock;

use Xepozz\EntitySorterBundle\Model\BaseSort;

class AbstractSortMock extends BaseSort
{
    public $sort;
    public $superCategories = [];

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
    public function getSuperCategories()
    {
        return $this->superCategories;
    }
}