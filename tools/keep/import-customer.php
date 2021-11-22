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
    /*
    if($pdo) {
        print "Connected to the $db database successfully\n";
    }
    */
} catch(PDOException $e) {
    print "{$e->getMessage()}\n";
}

//insert customer.json into customer table
$customer_data = json_decode(
    '[
        {
          "id": "1",
          "name": "Coca Cola",
          "since": "2014-06-28",
          "revenue": "492.12"
        },
        {
          "id": "2",
          "name": "Teamleader",
          "since": "2015-01-15",
          "revenue": "1505.95"
        },
        {
          "id": "3",
          "name": "Jeroen De Wit",
          "since": "2016-02-11",
          "revenue": "0.00"
        }
    ]'
);

$insert_customer = 
"INSERT INTO customer (`name`, `since`, `revenue`) VALUES (:name, :since, :revenue)";

$query_cust = $pdo->prepare($insert_query);

foreach ($customer_data as $key => $value) {
  $query_cust->execute([
    ':name'     => $value->name,
    ':since'    => $value->since,
    ':revenue'  => $value->revenue
  ]);   
}

$customer_id = $pdo->lastInsertId();

print "The last customer_id to be inserted was $customer_id\n";

//Insert product.json to table
$product_data = json_decode(
  '[
    {
      "id": "A101",
      "description": "Screwdriver",
      "category": "1",
      "price": "9.75"
    },
    {
      "id": "A102",
      "description": "Electric screwdriver",
      "category": "1",
      "price": "49.50"
    },
    {
      "id": "B101",
      "description": "Basic on-off switch",
      "category": "2",
      "price": "4.99"
    },
    {
      "id": "B102",
      "description": "Press button",
      "category": "2",
      "price": "4.99"
    },
    {
      "id": "B103",
      "description": "Switch with motion detector",
      "category": "2",
      "price": "12.95"
    }
  ]'
);
print_r($product_data);

$category_extract = []; //store category to insert into category table.
foreach ($product_data as $key => $value){
  $category_extract[$key] = $value->category;
}
$category_uniq = array_unique($category_extract);
//print_r($category_uniq);

$pdo->beginTransaction();

//insert categories into category table
try {
  $insert_category = "INSERT INTO category (`category`, `description`) VALUES (:category, :description)";

  $query_cat = $pdo->prepare($insert_category);

  foreach ($category_uniq as $value) {
    $query_cat->execute([
      ':category'   => $value,
      ':description' => ''
    ]);
    $category_id = $pdo->lastInsertId();
    print "The last category inserted was $category_id\n";
  }

  $insert_product = "INSERT INTO product (id, description, price, category) VALUES (:id, :description, :price, :category)";

  $query_prod = $pdo->prepare($insert_product);

  foreach ($product_data as $key => $value){
    $query_prod->execute([
      ':id'           => $value->id,
      ':description'  => $value->description,
      ':price'        => $value->price,
      ':category'     => $value->category
    ]);
    $product_id = $pdo->lastInsertId();
    print "The last product id inserted was $product_id\n";
  }
  $pdo->commit();
  print "No errors; all operations completed successfully. Smile!\n";
} catch(PDOException $e) {
  print "{$e->getMessage()}\n";
  print "All processes have been reversed. No harm done! Smile.\n";
  $pdo->rollback();
}

