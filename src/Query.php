<?php
declare(strict_types=1);

/**
  * Class Query | core/Query.class.php
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

use app\src\Customer;
use app\src\Category;
use app\src\Product;
use app\src\Orders;
use app\src\OrderItem;

/**
 * Class Query has the property, query, for sql queries. It has two properties, classname and operation, that are used as indicators of the query to prepare for use.
 * It will be extended by the RunQuery class. The classname property is the class of the main object that your query will operate on.
 * To use class: 1). Create an instance, passing in the classname and operation to perform and Constructor calls the matchCase() method to retrieve the exact query needed.
 * 2. In the child class, call the getQuery() method to return the query string.
 */
class Query
{
    /** @var int $query The query that will be run */    
    protected string $query = '';

    /** @var int $classname The class name of the object query will operate on */      
    protected string $classname;

    /** @var int $operation The descripton of what query will be doing - select, insert, delete etc */
    protected string $operation;
    
    /**
     * Constructor Sets classname and operation. It concatenates them and calls the matchCase() method
     * @param string $classname The class name of object query affects
     * @param string $operation The query action to perform
     * @return void. 
     */    
    public function __construct(
        string $classname,
        string $operation
    )
    {
        $this->classname    = $classname;
        $this->operation    = $classname.$operation;
        $this->matchCase();
    }

    /**
     * Matches the query required according to the $operation property
     * @return void. 
     */
    private function matchCase() :void
    {
        match($this->operation) {
            'ordersinsert'              => $this->setOrdersInsertQuery(),
            'orderiteminsert'           => $this->setOrderItemInsertQuery(),
            'orderitemselectcategory'   => $this->setOrderItemSelectCategoryQuery(),
            default                     => 'invalid input!',
        };
    }

    /**
     * Query to insert into orders table
     * @return void. 
     */
    private function setOrdersInsertQuery() :void
    {
        $this->query = 
        $insert_order = "INSERT INTO orders (id, customer_id, total) 
        VALUES (:id, :customer_id, :total)";
    }

    /**
     * Query to insert into order_item table
     * @return void. 
     */
    private function setOrderItemInsertQuery() :void
    {
        $this->query = 
        $insert_order_items = "INSERT INTO order_item (order_id, product_id, quantity, unit_price, product_pk) 
        VALUES (:order_id, :product_id, :quantity, :unit_price, :product_pk)";
    }

    /**
     * Query to select item category
     * @return void. 
     */
    private function setOrderItemSelectCategoryQuery() :void
    {
        $this->query = 
        $select_item_category = 
        "SELECT category FROM product WHERE id = :id";
    }

    /**
     * Getter for query
     * @return string. 
     */
    protected function getQuery() :string
    {
        return $this->query;
    }


}
