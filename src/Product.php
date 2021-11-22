<?php
declare(strict_types=1);

/**
  * Class Product | core/Product.class.php
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

use app\src\Category;

/**
 * Class Product models a product.
 * To use class: 
 * Create an instance, passing in the pk, product id, description and price of product or item.
 */
class Product extends Category
{
    /** @var int $pk The primary key of Product object */
    private int $pk;

    /** @var string $id The unique id of product  */
    private string $id;
    
    /** @var string $id The product description  */
    private string $description;
    private float $price;

    /**
     * Constructor Sets the class properties and instantiates the parent Category class.
     * @param string $id 
     * @param string $description
     * @param int $category This is passed into the constructor of parent 
     * @param float $price
     * @return void. 
     */    
    public function __construct(
        string $id,
        string $description,
        int $category,
        float $price
    ){
        $this->id           = $id;
        $this->description  = $description;
        $this->price        = $price; 
        parent::__construct(
            category : $category        
        );
    }

    /**
     * Setter for id
     * @return void 
     */ 
    public function setId(string $id) :void
    {
        $this->id = $id;
    }
    
    /**
     * Getter for id
     * @return string 
     */     
    public function getId() :string
    {
        return $this->id;
    }

    /**
     * Setter for description
     * @return void 
     */     
    public function setDescription(string $description) :void
    {
        $this->description = $description;
    }

    /**
     * Getter for description
     * @return string 
     */     
    public function getDescription() :string
    {
        return $this->description;
    }

    /**
     * Setter for price
     * @return void 
     */ 
    public function setPrice(float $price) :void
    {
        $this->price = $price;
    }

    /**
     * Getter for price
     * @return float 
     */ 
    public function getPrice() :float
    {
        return $this->price;
    }
}