Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require xepozz/entity-sorter-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Add it to an entity 
-------------------------

Add the Doctrine entity listener to your entitiy and don't forget to include all the use statements.
Then extend your Entity with BaseSort as shown as in the example below.

```php
<?php
// AppBundle/Entity/OrderListItem.php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_list_item")
 * @ORM\EntityListeners({"Xepozz\EntitySorterBundle\EventListener\SortListener"})
 */
class OrderListItem
{
    public function getId(){ /**/ }
    public function setId(){ /**/ }
    public function getSort(){ /**/ }
    public function setSort(){ /**/ }
    public function getSuperCategories(){ /**/ }
}
```

After this changes the sort value is already being set automatically for new database entries and is also correctly modified when you delete or update entries.

Step 3: Move items up and down 
-------------------------

To move your items up or down in the sort order use the entity functions ```moveUp($controller)``` and ```moveDown($controller)```. You can, for example, call these functions in your controller. Your controller class has to extend the Symfony controller:

```php
<?php
// AppBundle/Controller/testController.php

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Xepozz\EntitySorterBundle\Utils\EntitySorter;

class TestController extends Controller
{
    private $entitySorter;

    public function __construct(EntitySorter $entitySorter) 
    {
        $this->entitySorter = $entitySorter;
    }
    
    public function moveUpAction(OrderListItem $entity)
    {
        $this->entitySorter->moveUp($entity);

        return $this->redirect('...');
    }
    
    public function moveDownAction(OrderListItem $entity)
    {
        $this->entitySorter->moveDown($entity);

        return $this->redirect('...');
    }
}
```

(Optional) Step 4: Sorting within a supercategory
-------------------------

If your entity is a subcategory of another entity and should be sorted only within its own supercategory, you need to overwrite the function ```getSuperCategories()``` in your entity.

In the example below we have a product sub category that needs to be sorted within the product category.

```php
<?php
// AppBundle/Entity/ProductSubCategory.php

use Doctrine\ORM\Mapping as ORM;use Xepozz\EntitySorterBundle\Model\BaseSort;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_sub_category")
 * @ORM\EntityListeners({"Xepozz\EntitySorterBundle\EventListener\SortListener"})
 */
class ProductSubCategory extends BaseSort
{
    /**
     * @ORM\ManyToOne(targetEntity="ProductCategory", inversedBy="productSubCategories")
     * @ORM\JoinColumn(name="product_category_id", referencedColumnName="id")
     */
    protected $productCategory;

    /**
     * @return array
     */
    public function getSuperCategories()
    {
        return ['productCategory' => $this->getProductCategory()];
    }
}
```

An entity can have several supercategories. The array returned in `getSuperCategories` just has to contain the values from them. The order of the supercategories has no influence on the sorting:

```php
return [
    'productCategory' => $this->getProductCategory(),
    'anotherSuperCategory' => $this->getAnotherSuperCategory()
];
```
