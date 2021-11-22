<?php

require 'config/config.php';

use app\src\Customer;
use app\src\Category;
use app\src\Product;
use app\src\Orders;
use app\src\OrderItem;

//set data source name
$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    print "{$e->getMessage()}\n";
}

//Insert product.json to tables
$order_data = json_decode(
  '{
    "id": "3",
    "customer-id": "3",
    "items": [
      {
        "product-id": "A101",
        "quantity": "2",
        "unit-price": "9.75",
        "total": "19.50"
      },
      {
        "product-id": "A102",
        "quantity": "1",
        "unit-price": "49.50",
        "total": "49.50"
      }
    ],
    "total": "69.00"
  }'
);
$order_data = get_object_vars($order_data);
//print $order_data['customer-id'];
print "\n";

$pdo->beginTransaction();

try {
  $insert_order = "INSERT INTO orders (id, customer_id, total) VALUES (:id, :customer_id, :total)";

  $query_order = $pdo->prepare($insert_order);

  $query_order->execute([
    ':id'           => $order_data['id'],
    ':customer_id'  => $order_data['customer-id'],
    ':total'        => $order_data['total']
  ]);

  $items_arr = $order_data['items'];

  $insert_order_items = "INSERT INTO order_item (order_id, product_id, quantity, unit_price, product_pk) 
    VALUES (:order_id, :product_id, :quantity, :unit_price, :product_pk)";

  $query_order_item = $pdo->prepare($insert_order_items);

  foreach ($items_arr as $key => $value) {
    $items_arr_ = get_object_vars($value);
    //print "{$items_arr_['product-id']}\n";
    $query_order_item->execute([
      ':order_id'     => $order_data['id'],
      ':product_id'   => $items_arr_['product-id'],
      ':quantity'     => $items_arr_['quantity'],
      ':unit_price'   => $items_arr_['unit-price'],
      ':product_pk'   => null
    ]);
    $order_items_id = $pdo->lastInsertId();
    print "The last order item id inserted was $order_items_id\n";    
  }
  $pdo->commit();
  print "No errors; all operations completed successfully. Smile!\n";
} catch(PDOException $e){
  print "{$e->getMessage()}\n";
  print "All processes have been reversed. No harm done! Smile.\n";
  $pdo->rollback();
}