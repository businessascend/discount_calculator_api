<?php
declare(strict_types=1);

/**
  * Class Category | core/Category.class.php
  *
  * @api
  * @license GPL
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  * @package app\src
  * @author Adedoyin Adedipe <a.adedip@businessascend.com>
  * @copyright No restrictions.
  * @filesource
  */
namespace app\src;
/**
 * Class Category models Product / Item category.
 * To use class: 
 * 1. Create an instance, passing in the category id.
 * 2. Mostly, this class will be extended by the Product and OrderItem classes so that when you create a product, you instance this class in the constructor of the child class.
 */
class Category 
{
    /** @var int $category The id of the product or item category */
    private int $category; 

    /** @var string $name The name of product category or item */
    protected string $name = ''; 
    
    /**
     * Constructor sets the category property
     * @param string $category The id of the product category
     * @return void. 
     */
    public function __construct(
        int $category
    ){
        $this->category = $category;
    }

    /**
     * Getter for category
     * @return int 
     */
    public function getCategory() :int
    {
        return $this->category;
    }
    
    /**
     * Getter for category name
     * @return string 
     */
    public function getName() :string
    {
        return $this->name;
    }
}