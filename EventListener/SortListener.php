<?php

namespace Xepozz\EntitySorterBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class SortListener
{
    /**
     * @param $item
     * @param LifecycleEventArgs $event
     */
    public function prePersist($item, LifecycleEventArgs $event)
    {
        $maxSortRank = $this->getMaxSort($event, $item);
        $item->setSort($maxSortRank + 1);
    }

    /**
     * @param LifecycleEventArgs|PreUpdateEventArgs $event
     * @param $item
     * @param array $replacement
     * @return int
     */
    private function getMaxSort(&$event, $item, array $replacement = [])
    {
        $em = $event->getEntityManager();
        $entityClass = get_class($item);

        $superCategories = [];

        foreach ($item->getSuperCategories() as $key => $value) {
            if (array_key_exists($key, $replacement)) {
                $valueId = $replacement[$key];
            } else {
                $valueId = $value->getId();
            }

            $superCategories[$key] = $valueId;
        }

        $otherItem = $em
            ->getRepository($entityClass)
            ->findOneBy($superCategories, ['sort' => 'DESC']);

        return ($otherItem === null) ? 0 : $otherItem->getSort();
    }

    /**
     * @param $item
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate($item, PreUpdateEventArgs $args)
    {
        $superCategoryHasChanged = false;
        $newSuperCategoryValues = [];
        $oldSuperCategoryValues = [];

        $entity = $args->getEntity();

        foreach ($entity->getSuperCategories() as $superCategoryName => $superCategoryItem) {
            if ($args->hasChangedField($superCategoryName)) {
                $superCategoryHasChanged = true;

                $newSuperCategoryObject = $args->getNewValue($superCategoryName);
                $oldSuperCategoryObject = $args->getOldValue($superCategoryName);

                if ($newSuperCategoryObject !== null) {
                    $newSuperCategoryValues[$superCategoryName] = $newSuperCategoryObject->getId();
                }

                if ($oldSuperCategoryObject !== null) {
                    $oldSuperCategoryValues[$superCategoryName] = $oldSuperCategoryObject->getId();
                }
            } elseif ($superCategoryItem !== null) {
                $newSuperCategoryValues[$superCategoryName] = $superCategoryItem->getId();
                $oldSuperCategoryValues[$superCategoryName] = $superCategoryItem->getId();
            }
        }

        if (!$superCategoryHasChanged) {
            return;
        }

        // correct sort order of the item from the old supercategories
        $this->updateItemsWithHigherSortNumber($args, $entity, $oldSuperCategoryValues);

        // get highest sort value of the items from the new supercategories
        $maxSortRank = $this->getMaxSort($args, $entity, $newSuperCategoryValues);

        // set the sort value to the new highest sort of the new supercategories
        $entity->setSort($maxSortRank + 1);
    }

    /**
     * @param LifecycleEventArgs|PreUpdateEventArgs $event
     * @param $item
     * @param array $replacement
     * Every item, with a higher sort value than the moved / deleted item,
     * has the sort value reduced by 1 to close the gap
     */
    private function updateItemsWithHigherSortNumber(&$event, &$item, $replacement = [])
    {
        $em = $event->getEntityManager();
        $entityClass = get_class($event->getEntity());

        $sortRank = $item->getSort();

        $superCategoryCondition = '';

        foreach ($item->getSuperCategories() as $key => $value) {
            if (array_key_exists($key, $replacement)) {
                $valueId = $replacement[$key];
            } else {
                $valueId = $value->getId();
            }

            $superCategoryCondition .= "i.$key = $valueId AND ";
        }

        $query = $em
            ->createQuery("SELECT i FROM $entityClass i WHERE $superCategoryCondition i.sort > :sort")
            ->setParameter('sort', $sortRank);

        $itemsWithHigherSortNumber = $query->getResult();

        foreach ($itemsWithHigherSortNumber as $item) {
            $newSort = $item->getSort() - 1;

            /*
             * DQL is used here to avoid maximum function nesting error in XDebug
            */
            $updateQuery = $em
                ->createQuery("UPDATE $entityClass i SET i.sort = :sort WHERE i.id = :id")
                ->setParameter('sort', $newSort)
                ->setParameter('id', $item->getId());

            $updateQuery->execute();
        }
    }

    /**
     * @param $item
     * @param LifecycleEventArgs $event
     */
    public function preRemove($item, LifecycleEventArgs $event)
    {
        $this->updateItemsWithHigherSortNumber($event, $item);
    }
}
