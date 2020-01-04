<?php

namespace Xepozz\SorterBundle\Utils;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Xepozz\SorterBundle\Model\AbstractSort;
use Xepozz\SorterBundle\Model\BaseSort;

class SimpleSorter
{
    const UP = 0;
    const DOWN = 1;

    /**
     * @param Controller $controller
     * @param BaseSort|AbstractSort $objectOne
     * @return bool
     */
    public static function moveUp(Controller &$controller, $objectOne)
    {
        return self::move($controller, $objectOne, self::UP);
    }

    /**
     * @param Controller $controller
     * @param BaseSort|AbstractSort $objectOne
     * @param int $order
     * @return bool
     */
    private static function move(Controller &$controller, $objectOne, $order)
    {
        $fullClassName = get_class($objectOne);
        $objectOneId = $objectOne->getId();

        $em = $controller->getDoctrine()->getManager();

        $objectOne = $em->getRepository($fullClassName)->find($objectOneId);

        if ($order == self::UP) {
            $objectTwoOrder = $objectOne->getSort() - 1;
        } elseif (self::DOWN) {
            $objectTwoOrder = $objectOne->getSort() + 1;
        } else {
            throw new InvalidParameterException('Sort order has to be either simpleSorter::UP or simpleSorter::DOWN');
        }

        $conditionArray = $objectOne->getSuperCategories();
        $conditionArray['sort'] = $objectTwoOrder;

        $objectTwo = $em->getRepository($fullClassName)
            ->findOneBy(
                $conditionArray
            );

        if (is_null($objectTwo)) {
            return false;
        }

        $tempSortValue = $objectOne->getSort();

        $objectOne->setSort($objectTwo->getSort());
        $objectTwo->setSort($tempSortValue);

        $em->persist($objectOne);
        $em->persist($objectTwo);
        $em->flush();

        return true;
    }

    /**
     * @param Controller $controller
     * @param BaseSort|AbstractSort $objectOne
     * @return bool
     */
    public static function moveDown(Controller &$controller, $objectOne)
    {
        return self::move($controller, $objectOne, self::DOWN);
    }
}
