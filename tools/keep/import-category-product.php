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
//print_r($product_data);

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

