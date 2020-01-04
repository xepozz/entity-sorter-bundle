<?php

namespace Xepozz\SorterBundle\Model;

trigger_error('AbstractSort will be removed in the next major version, use BaseSort instead', E_USER_DEPRECATED);

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Xepozz\SorterBundle\Utils\SimpleSorter;

/**
 * Class AbstractSort
 *
 * @package Xepozz\SorterBundle\Model
 * @deprecated
 */
abstract class AbstractSort
{
    /**
     * @return integer
     */
    abstract public function getId();

    /**
     * @return integer
     */
    abstract public function getSort();

    /**
     * @param integer $sort
     */
    abstract public function setSort($sort);

    /**
     * @return array
     */
    public function getSuperCategories()
    {
        return [];
    }

    /**
     * @param Controller $controller
     */
    public function moveUp(Controller &$controller)
    {
        SimpleSorter::moveUp(
            $controller,
            $this
        );
    }

    /**
     * @param Controller $controller
     */
    public function moveDown(Controller &$controller)
    {
        SimpleSorter::moveUp(
            $controller,
            $this
        );
    }
}
