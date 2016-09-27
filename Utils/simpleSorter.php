<?php

namespace Ip\SorterBundle\Utils;

use Ip\SorterBundle\Model\AbstractSort;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class simpleSorter
{
    const UP = 0;
    const DOWN = 1;

    /**
     * @param Controller $controller
     * @param AbstractSort $objectOne
     * @return bool
     */
    public static function sortUp(Controller &$controller, AbstractSort $objectOne) {
        return self::sort($controller, $objectOne, self::UP);
    }

    /**
     * @param Controller $controller
     * @param AbstractSort $objectOne
     * @return bool
     */
    public static function sortDown(Controller &$controller, AbstractSort $objectOne) {
        return self::sort($controller, $objectOne, self::DOWN);
    }

    /**
     * @param Controller $controller
     * @param AbstractSort $objectOne
     * @param int $order
     * @return bool
     */
    private static function sort(Controller &$controller, AbstractSort $objectOne, $order) {
        $fullClassName = get_class($objectOne);
        $objectOneId = $objectOne->getId();

        $em = $controller->getDoctrine()->getManager();

        $objectOne = $em->getRepository($fullClassName)->find($objectOneId);

        if ($order == self::UP) {
            $objectTwoOrder = $objectOne->getSort() - 1;
        }
        elseif (self::DOWN) {
            $objectTwoOrder = $objectOne->getSort() + 1;
        }
        else {
            throw new InvalidParameterException('Sort order has to be either simpleSorter::UP or simpleSorter::DOWN');
        }

        $conditionArray = $objectOne->hasSuperCategory();
        $conditionArray['sort'] = $objectTwoOrder;

        $objectTwo = $em->getRepository($fullClassName)
            ->findOneBy(
                $conditionArray
            )
        ;

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
}