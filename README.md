Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require ip/sorter-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Add it to an entity 
-------------------------

Add the Doctrine entity listener to your entitiy and don't forget to include all the use statements.
Then extend your Entity with AbstractSort. Afterwards you have to implement the functions ```getID()```, ```getSort()``` and ```setSort($sort)``` as shown as in the example below.

```php
<?php
// AppBundle/Entity/Test.php

// ...

use Doctrine\ORM\Mapping as ORM;
use Ip\SorterBundle\Model\AbstractSort;

/**
 * @ORM\Entity
 * @ORM\Table(name="test")
 * @ORM\EntityListeners({"Ip\SorterBundle\EventListener\SortListener"})
 */
class Test extends AbstractSort
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

    // ...
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set sort
     *
     * @param integer $sort
     * @return Test
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }
}
```

After this changes the sort value is already being set automatically for new database entries and is also correctly modified when you delete entries.

Step 3: Move items up and down 
-------------------------

To move your items up or down in the sort order use the entity functions ```moveUp($controller)``` and ```moveDown($controller)```. You can, for example, call these functions in your controller. Your controller class has to extend the Symfony controller:

```php
<?php
// AppBundle/Controller/testController.php

// ...

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// ...

class testController extends Controller
{
    
    // ...
    
    public function moveUpAction($id)
    {
        // ...
        
        $testEntity->moveUp($this);

        // ...
    }
    
    public function moveDownAction($id)
    {
        // ...
        
        $testEntity->moveDown($this);

        // ...
    }

    // ...
}
```

(Optional) Step 4: Sorting within a supercategory
-------------------------

If your entity is a subcategory of another entity and should be sorted only within its own supercategeory, then you need to overwrite the function ```hasSuperCategory()``` in your entity.

In the example below we have a product sub category that needs to be sorted within the product category.

```php
<?php
// AppBundle/Entity/ProductSubCategory.php

// ...

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\EntityListeners;
use Ip\SorterBundle\Model\AbstractSort;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_sub_category")
 * @Entity @EntityListeners({"Ip\SorterBundle\EventListener\SortListener"})
 */
class ProductSubCategory extends AbstractSort
{
    // ...
    
    /**
     * @ORM\ManyToOne(targetEntity="ProductCategory", inversedBy="productSubCategories")
     * @ORM\JoinColumn(name="product_category_id", referencedColumnName="id")
     */
    protected $productCategory;
    
    // ...
    
    /**
     * @return array
     */
    public function hasSuperCategory()
    {
        return array('productCategory' => $this->getProductCategory());
    }
}
```

If an entity has more than one super category, the array, which is returned in the ```hasSuperCategory()``` can just be extended:

```php
return array(
    'productCategory' => $this->getProductCategory(),
    'anotherSuperCategory' => $this->getAnotherSuperCategory()
);
```
