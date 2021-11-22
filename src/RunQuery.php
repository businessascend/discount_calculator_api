<?php

declare(strict_types=1);

/**
  * Class runQuery | core/runQuery.class.php
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

use app\src\Query;

/**
 * Class runQuery extends the Query class. It receives a pdo connection object and also the object to use in query parameters.
 * It will be extended by the RunQuery class.
 * To use class: 1). Create an instance, passing in the pdo connecton and object with the bind parameters. Also, pass in the 
 * classname and operation, that are used as indicators of the query to prepare for use.
 * 2. Constructor instantiates the parent class using the classname and operation parameters.
 */
class RunQuery extends Query
{
    /** @var object $pdo The database connection object */
    private \PDO $pdo;

    /** @var object $object The object containing the bind parameters */
    private object $object;

    /** @var object $resultSet The result set from query execution */
    private array|object $resultSet; 

    /**
     * Constructor Sets database connection object and another object with the bind parameters.
     * The classname and operation are used to instantiate the Query class
     * @param object $pdo Database connection object
     * @param object $object Contains bind parameters for query execution
     * @param string $classname The class name of object query affects
     * @param string $operation The query action to perform.
     * @return void. 
     */    
    public function __construct(
        \PDO $pdo,
        object $object,
        string $classname,
        string $operation        
    )
    {
        $this->pdo      = $pdo;
        $this->object   = $object;
        parent::__construct (
            classname   : $classname,
            operation   : $operation
        );
    }

    /**
     * Matches the action required according to the $operation property
     * @param mixed $arg
     * @return void. 
     */
    public function runTheQuery(mixed $arg = '') :void
    {
        match($this->operation) {
            'ordersinsert'              => $this->ordersInsert(),
            'orderiteminsert'           => $this->orderItemInsert(),
            'orderitemselectcategory'   => $this->orderItemSelectCategory($arg),
            default  => 'invalid input!',
        };
    }

    /**
     * Prepares and executes Query to insert into orders table
     * @return void. 
     */
    private function ordersInsert() :void
    {
        $statement = 
          $this->pdo->prepare($this->query);
        $statement->execute([
            ':id'           => $this->object->getId(),
            ':customer_id'  => $this->object->getCustomerId(),
            ':total'        => $this->object->getTotal()
        ]);      
    }

    /**
     * Prepares and executes Query to insert into order_item table
     * @return void. 
     */
    private function orderItemInsert() :void
    {
        $statement = 
          $this->pdo->prepare($this->query);
        $statement->execute([
            ':order_id'     => $this->object->getOrderId(),
            ':product_id'   => $this->object->getProductId(),
            ':quantity'     => $this->object->getQuantity(),
            ':unit_price'   => $this->object->getUnitPrice(),
            ':product_pk'   => null
        ]);
    }

    /**
     * Prepares and executes Query to select item category
     * @return void. 
     */
    private function orderItemSelectCategory($arg) :void
    {
        $statement = 
          $this->pdo->prepare($this->query);
        //$statement->bindParam(':id', $arg, \PDO::PARAM_INIT)
        $statement->execute([
            ':id' => $arg,
        ]);
        $category = $statement->fetch(\PDO::FETCH_ASSOC);
        $this->resultSet = $category;
    }

    /**
     * Getter for resultSet
     * @return array|object. 
     */
    public function getResultSet() :array|object
    {
        return $this->resultSet;
    }
}
