<?php

declare(strict_types=1);

/**
  * Class OrderItem | core/OrderItem.class.php
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

use app\src\Order;
use app\src\Product;
use app\src\Category;

/**
 * Class OrderItem models an item in a customer order and extends the Category class
 * To use class: 
 * Create an instance, pass in the order id, product id, quantity, unitPrice for the item and
 * the item category. You can get the item category from the database table association between 
 * product and product category.
 */
class OrderItem extends Category
{
    /** @var int $id The id of the item */
    private int $id;
    
    /** @var int $orderId The id of the order */
    private int $orderId;
    
    /** @var string $productId The id of the product */
    private string $productId;
    
    /** @var int $quantity The quantity of the item */
    private int $quantity;

    /** @var float $unitPrice The unit price of the item */
    private float $unitPrice;
    
    /**
     * Constructor sets the orderId, productId, quantity, unitPrice and category of item. Parant class is instanced with $category
     * @param int $orderId The id of customer's order
     * @param string $productId The id of the product item
     * @param int $quantity The quantity of product item
     * @param float $unitPrice Price per unit item
     * @param int $category The product category item belongs
     * @return void. 
     */    
    public function __construct(
        int $orderId,
        string $productId,
        int $quantity,
        float $unitPrice,
        int $category
    ){
        parent::__construct(
            category  :  $category
        );
        $this->orderId    = $orderId;
        $this->productId  = $productId;
        $this->quantity   = $quantity;
        $this->unitPrice  = $unitPrice;
    }
    
    /**
     * Getter for orderId
     * @return int 
     */   
    public function getOrderId() :int
    {
        return $this->orderId;
    }
    
    /**
     * Getter for productId
     * @return string 
     */   
    public function getProductId() :string
    {
        return $this->productId;
    }
    
    /**
     * Getter for quantity
     * @return int 
     */   
    public function getQuantity() :int
    {
        return $this->quantity;
    }

    /**
     * Getter for unitPrice
     * @return float 
     */   
    public function getUnitPrice() :float
    {
        return $this->unitPrice;
    }



}