<?php
declare(strict_types=1);

/**
  * Class Orders | core/Orders.class.php
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
 * Class Orders models an order without the item.
 * To use class: 
 * Create an instance, passing in the order id, customer id and total amount of order.
 */
class Orders 
{
    
    /**
     * Constructor sets the order id, customer id and order total amount.
     * @param int $id The id of customer's order
     * @param string $customerId The id of the product item
     * @param int $total The order total amount
     * @return void. 
     */    

    public function __construct(
        private int $id,
        private int $customerId,
        private float $total
    ){
        $this->id = $id;
        $this->customerId = $customerId;
        $this->total = $total;
    }
    
    /**
     * Getter for id
     * @return int 
     */ 
    public function getId() :int
    {
        return $this->id;
    }
 
    /**
     * Getter for customerId
     * @return int 
     */    
    public function getCustomerId() :int
    {
        return $this->customerId;
    }
    
    /**
     * Getter for total
     * @return float 
     */    
    public function getTotal() :float
    {
        return $this->total;
    }
}