<?php

namespace Xepozz\SorterBundle\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Xepozz\SorterBundle\Model\BaseSort;

class SimpleSorter
{
    const UP = 0;
    const DOWN = 1;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param BaseSort $objectOne
     * @return void
     */
    public function moveUp($objectOne)
    {
        $this->move($objectOne, self::UP);
    }

    /**
     * @param BaseSort $objectOne
     * @return void
     */
    public function moveDown($objectOne)
    {
        $this->move($objectOne, self::DOWN);
    }

    /**
     * @param BaseSort $objectOne
     * @param int $order
     * @return void
     */
    private function move($objectOne, $order)
    {
        $fullClassName = get_class($objectOne);
        $objectOneId = $objectOne->getId();

        $em = $this->entityManager;

        $objectOne = $em->getRepository($fullClassName)->find($objectOneId);

        if ($order === self::UP) {
            $objectTwoOrder = $objectOne->getSort() - 1;
        } elseif (self::DOWN) {
            $objectTwoOrder = $objectOne->getSort() + 1;
        } else {
            throw new InvalidParameterException('Sort order has to be either simpleSorter::UP or simpleSorter::DOWN');
        }

        $conditionArray = $objectOne->getSuperCategories();
        $conditionArray['sort'] = $objectTwoOrder;

        $objectTwo = $em
            ->getRepository($fullClassName)
            ->findOneBy($conditionArray);

        if ($objectTwo === null) {
            return;
        }

        $tempSortValue = $objectOne->getSort();

        $objectOne->setSort($objectTwo->getSort());
        $objectTwo->setSort($tempSortValue);

        $em->persist($objectOne);
        $em->persist($objectTwo);
        $em->flush();
    }
}
