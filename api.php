<?php

//Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8', true,200);

require 'tools/config/config.php';
require 'src/Customer.php';
require 'src/Category.php';
require 'src/Product.php';
require 'src/Orders.php';
require 'src/OrderItem.php';
require 'src/Discount.php';
require 'src/Query.php';
require 'src/RunQuery.php';

use app\src\Customer;
use app\src\Category;
use app\src\Product;
use app\src\Orders;
use app\src\OrderItem;
use app\src\Query;
use app\src\RunQuery;
use app\src\Discount;

$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
  $pdo = new \PDO($dsn, $user, $password);
  $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch(\PDOException $e) {
  //print "{$e->getMessage()}\n";
}

//receive order json data
if (strcasecmp($_SERVER["REQUEST_METHOD"], 'POST') != 0) {
  throw new Exception('Request method must be POST!');
}
//ensure content type of POST request is application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if (strcasecmp($contentType, 'application/json') != 0) {
  throw new Exception('Content type must be: application/json');
}

//now retrieve json data
$order_rec = trim(file_get_contents("php://input"));

$order_data = json_decode($order_rec, false); //false to have an object.

//ensure above returns valid object
if(!is_object($order_data)) {
  throw new Exception('Data received seems invalid; please, check.');
}

//BEGIN: inserting order data into database tables.
$order_data = get_object_vars($order_data);

$pdo->beginTransaction();

try {
  $order = new Orders (
    id          : $order_data['id'],
    customerId  : $order_data['customer-id'],
    total       : $order_data['total']
  );

  $runQuery = new RunQuery ( 
    object      : $order,
    classname   : 'orders',
    operation   : 'insert',
    pdo         : $pdo
  );

  $runQuery->runTheQuery();

  $items_arr = $order_data['items'];

  foreach ($items_arr as $key => $value) {
    $items_arr_ = get_object_vars($value);
    
    //BEGIN: get product category for each item as its not given in order.json but can be derived
    $runQuery = new RunQuery ( 
      object      : new stdClass(),
      classname   : 'orderitem',
      operation   : 'selectcategory',
      pdo         : $pdo
    );
    $runQuery->runTheQuery($items_arr_['product-id']);
    $category = $runQuery->getResultSet();    
    
    //CREATE orderItem objects
    $orderItem = new OrderItem (
      orderId       : $order_data['id'],
      productId     : $items_arr_['product-id'],
      quantity      : $items_arr_['quantity'],
      unitPrice     : $items_arr_['unit-price'],
      category      : $category['category']
    );    
    
    //BEGIN: INSERT TO TABLE    
    $runQuery = new RunQuery ( 
      object      : $orderItem,
      classname   : 'orderitem',
      operation   : 'insert',
      pdo         : $pdo
    );
    $runQuery->runTheQuery();
    //END: INSERT TO TABLE
  }
  $pdo->commit();
  //print "No errors; all operations completed successfully. Smile!\n";
} catch(PDOException $e){
  //print "{$e->getMessage()}\n";
  //print "All processes have been reversed. No harm done! Smile.\n";
  $pdo->rollback();
}
//END: inserting order data into database tables.

//BEGIN: calculating discounts.
$order_mine = []; //to push order and orderItem objects here.

$pdo->beginTransaction();

try {
  $order = new Orders (
    id          : $order_data['id'],
    customerId  : $order_data['customer-id'],
    total       : $order_data['total']
  );

  $order_mine['order'] = $order;

  $items_arr = $order_data['items'];

  foreach ($items_arr as $key => $value) {
    $items_arr_ = get_object_vars($value);
    
    //BEGIN: get product category for each item
    $runQuery = new RunQuery ( 
      object      : new stdClass(),
      classname   : 'orderitem',
      operation   : 'selectcategory',
      pdo         : $pdo
    );
    $runQuery->runTheQuery($items_arr_['product-id']);
    $category = $runQuery->getResultSet();    
    
    //CREATE orderItem objects
    $orderItem = new OrderItem (
      orderId       : $order_data['id'],
      productId     : $items_arr_['product-id'],
      quantity      : $items_arr_['quantity'],
      unitPrice     : $items_arr_['unit-price'],
      category      : $category['category']
    );
    
    $order_mine['item'][$key] = $orderItem;
  
  }
  $pdo->commit();
  //print "\nAll discount calculations completed successfully. See result below:\n\n";
} catch(PDOException $e){
  //print "{$e->getMessage()}\n";
  //print "All processes have been reversed. No harm done! Smile.\n";
  $pdo->rollback();
}

//calculate discount
$discount = new Discount($order_mine);
$discount->calculateDiscount();
$discountCalculationDetails = $discount->getDiscountInfo();

$discountResult = [
  'order_id'       => $order_mine['order']->getId(),
  'customer_id'    => $order_mine['order']->getCustomerId(),
  'discount_calc'  => $discountCalculationDetails
];

print_r ( json_encode($discountResult, JSON_PRETTY_PRINT) );

//END: calculating discounts.
?>