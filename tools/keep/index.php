<?php
  //Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json; charset=utf-8', true,200);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="description" content="">
    <meta name="author" content="">
    <title>API for calculating discount for order.</title>
  </head>

  <body>


<?php

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
  print "{$e->getMessage()}\n";
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

$order_data = json_decode($order_rec, false);

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
  print "No errors; all operations completed successfully. Smile!\n";
} catch(PDOException $e){
  print "{$e->getMessage()}\n";
  print "All processes have been reversed. No harm done! Smile.\n";
  $pdo->rollback();
}
//END: inserting order data into database tables.

//BEGIN: calculating discounts.
$order_mine = []; //push order and orderItem objects here.

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
  print "\nAll discount calculations completed successfully. See result below:\n\n";
} catch(PDOException $e){
  print "{$e->getMessage()}\n";
  print "All processes have been reversed. No harm done! Smile.\n";
  $pdo->rollback();
}

//calculate discount
$discount = new Discount($order_mine);
$discount->calculateDiscount();
$discountCalculationDetails = $discount->getDiscountInfo();

/*
$discountResult = new stdClass();
$discountResult->original_order         = $order_rec;
$discountResult->discount_calculation   = $discountCalculationDetails;
*/

$discountResult = [
  'order_id'       => $order_mine['order']->getId(),
  'customer_id'    => $order_mine['order']->getCustomerId(),
  'discount_calc'  => $discountCalculationDetails
];

print "Original order received:\n";
print_r ( $order_rec);
print "\n\n";
print "Discount calculation:\n";
print_r ( json_encode($discountResult, JSON_PRETTY_PRINT) );
print "\n\n";

//Print out some notes
print 
"<p class='dfpj-para'>
<b>The objective here was to calculate discount for a given order data. These discounts are based on three possible ways for now:</b>
<ol>
<li>A customer who has already bought for over € 1000, gets a discount of 10% on the whole order.</li>
<li>For every product of category \"Switches\" (id 2), when you buy five, you get a sixth for free.</li>
<li>If you buy two or more products of category \"Tools\" (id 1), you get a 20% discount on the cheapest product.</li>
</ol>
</p>";


print
"<p class='dfpj-para'>
<b>This is some explanation on the result shown in the above data:</b>
<ul>
<li>The first key, order_rec, contains the orginal order.json data received.</li>
<li>The second key, discount_calc, contains the discount calculation details for the three rules.</li>
</ul>
Thus, 'discount_calc' has three sub-objects corresponding to each of the rules:
<ol>
<li>the 'over1k' key holds the information on discount acquired when order total is 
above 1000 Eur thus showing the calculation for the first discount criteria.
The 'qualifies' key tells whether customer qualifies for this
discount or not. the 'discountRate' gives the rate for the discount rule and the adjustedTotal then gives the new total order amount after applying the calculated discount.</li>
<br />
<li>the 'category2' key gives the compilation of the discount applied for category-2 products. 
'qualifies' tells whether customer order benefited from this discount by buying at least 5 of category-2 products
or not. The value of the 'freeProd' key gives the number of free items of same category-2 product to be added as benefit and the 'adjustedQuantity' key gives the new item quantity.</li>
<br />
<li>the 'twoOrMoreCategory1' key holds discount data for the case when customer buys two or more products of category-1 
'qualifies' key will be true if it is the case and false otherwise. The discount rate given by the 'discountRate' key was applied on the price of the cheapest product 
so the amountDeductible is the amount to be removed from order Total.</li>
</ol>
</p>";

?>

  </body>
</html>
