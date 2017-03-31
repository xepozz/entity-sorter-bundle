<?php


namespace Ip\SorterBundle\Model;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Ip\SorterBundle\Utils\simpleSorter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @MappedSuperclass
 */
class BaseSort
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sort;

    /**
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param integer $id
     * @return $this
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
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
     * @return $this
     */
    public function setSort($sort) {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return array
     */
    public function hasSuperCategory()
    {
        return array();
    }

    /**
     * @param Controller $controller
     */
    public function moveUp(Controller &$controller)
    {
        simpleSorter::moveUp(
            $controller,
            $this
        );
    }

    /**
     * @param Controller $controller
     */
    public function moveDown(Controller &$controller)
    {
        simpleSorter::moveUp(
            $controller,
            $this
        );
    }
}