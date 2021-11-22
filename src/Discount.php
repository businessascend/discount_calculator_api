<?php

declare(strict_types=1);

/**
  * Class Discount | core/Discount.class.php
  *
  * @api
  * @license GPL
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  * @package app\src
  * @author Adedoyin Adedipe <a.adedipe@businessascend.com>
  * @copyright No restrictions
  * @filesource
  */
namespace app\src;

require 'DiscountInterface.php';
use app\src\DiscountInterface;

/**
 * Class Discount implements the DisountInface; it computes the order discount according to 3 rules:
 * a. Give 10% on order total when total amount is greater than 1000 Eur
 * b. When customer buys five of category-2 product, he gets a sixth for free
 * c. when customer buys two or more of category-2 product, he gets 10% on cheapest product
 * To use class: 1). Create an instance, passing in an array of Orders and OrderItem objects.
 * 2. Run the calculateDiscount() method to calculate the discount and
 * 3. Run the getDiscountInfo() method to get the result as array.
 */
class Discount implements DiscountInterface
{
    private const MAGIC_5     = 5;
    private const MAGIC_2     = 2;
    private const TEN_PERC    = 0.1;
    private const TWENTY_PERC = 0.2;
    private array $orderMine;
  
    /**
     * Constructor only requires $orderMine property to instantiate class
     * @param array $order_min Contains Orders and OrderItem objects
     * @param array $over1k array is initialized.
     * @param array $fiveOrMoreCategory2 array is initialized.
     * @param array $twoOrMoreCategory1 array is initialized.
     * @return void. 
     */
    public function __construct(
        array $order_mine,
        private array $over1k = [
            'qualifies'     => false, 
            'discountRate'  => Discount::TEN_PERC, 
            'adjustedTotal' => 0.00
        ],
        private array $fiveOrMoreCategory2 = [
            'qualifies'     => false, 
            'freeProd'      => 0, 
            'newItemQty'    => 0
        ],
        private array $twoOrMoreCategory1 = [
            'qualifies'         => false, 
            'discountRate'      => Discount::TWENTY_PERC, 
            'amountDeductible'  => 0.00
        ],
        private array $discountInfo = []
    ){
        $this->orderMine = $order_mine;
    }

    /**
     * Calculates the discount and modifies the $over1k, $fiveOrMoreCategory2 and $twoOrMoreCategory1 properties accordingly
     * @return void. 
     */
    public function calculateDiscount() :void
    {
        //discount rule 1: check order total
        $orderTotal = $this->orderMine['order']->getTotal();
        if ( $orderTotal > 1000.00 ) {
            $this->over1k['qualifies']      = true;
            $this->over1k['discountRate']   = Discount::TEN_PERC;
            $this->over1k['adjustedTotal']  = $orderTotal - ($orderTotal*Discount::TEN_PERC);
        }
        
        //discount rules 2 and 3: check items objects
        $order_item = $this->orderMine['item'];

        //get cheapest product category and price
        $order_item_price = [];
        foreach ($order_item as $key => $orderItem) {
            $order_item_price[$key] = $orderItem->getUnitPrice();
        }
        
        $min_price = min( $order_item_price );

        foreach ($order_item as $key => $orderItem) {
            //discount rule 2
            if ( $orderItem->getCategory() === 2 && 
                 $orderItem->getQuantity() >= Discount::MAGIC_5 ) {
                
                //the number of free products to be added will be the quotient 
                //of item quantity and 5.
                $free_ones = ( $orderItem->getQuantity() / Discount::MAGIC_5 );
                
                //Add free_ones to item quantity.
                $this->fiveOrMoreCategory2['qualifies']     = true;
                $this->fiveOrMoreCategory2['freeProd']      = 
                    "$free_ones qty of " . $orderItem->getProductId();
                $this->fiveOrMoreCategory2['newItemQty']    = 
                    $orderItem->getQuantity() + $free_ones . ' of ' . $orderItem->getProductId();
            }
            //discount rule 3
            if ( $orderItem->getCategory() === 1 && 
                 $orderItem->getQuantity() >= Discount::MAGIC_2 ) {
                $this->twoOrMoreCategory1['qualifies']          = true;
                $this->twoOrMoreCategory1['discountRate']       = Discount::TWENTY_PERC;
                $this->twoOrMoreCategory1['amountDeductible']   = 
                    \number_format($min_price*Discount::TWENTY_PERC, 2);
            }

        }

    }

    /**
     * Puts the $over1k, $fiveOrMoreCategory2 and $twoOrMoreCategory1 properties in the discountInfo property and returns it.
     * @return array. 
     */    
    public function getDiscountInfo() :array
    {
        $this->discountInfo = [
            'over1k'                => $this->over1k,
            'fiveOrMoreCategory2'   => $this->fiveOrMoreCategory2,
            'twoOrMoreCategory1'    => $this->twoOrMoreCategory1
        ];
        return $this->discountInfo;
    }
}