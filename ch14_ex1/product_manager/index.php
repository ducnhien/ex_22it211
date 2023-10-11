<?php
require('../model/database.php');
require('../model/category.php');
require('../model/category_db.php');
require('../model/product.php');
require('../model/product_db.php');

$categoryDB = new CategoryDB();
$productDB = new ProductDB();

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'list_products';
    }
}  

if ($action == 'list_products') {
    $category_id = filter_input(INPUT_GET, 'category_id', 
            FILTER_VALIDATE_INT);
    if ($category_id == NULL || $category_id == FALSE) {
        $category_id = 1;
    }

    
    $current_category = $categoryDB->getCategory($category_id);
    $categories = $categoryDB->getCategories();
    $products = $productDB->getProductsByCategory($category_id);

    include('product_list.php');
} else if ($action == 'delete_product') {
    
    $product_id = filter_input(INPUT_POST, 'product_id', 
            FILTER_VALIDATE_INT);
    $category_id = filter_input(INPUT_POST, 'category_id', 
            FILTER_VALIDATE_INT);

    
    $productDB->deleteProduct($product_id);

    
    header("Location: .?category_id=$category_id");
} else if ($action == 'show_add_form') {
    $categories = $categoryDB->getCategories();
    include('product_add.php');
} else if ($action == 'add_product') {
    $category_id = filter_input(INPUT_POST, 'category_id', 
            FILTER_VALIDATE_INT);
    $code = filter_input(INPUT_POST, 'code');
    $name = filter_input(INPUT_POST, 'name');
    $price = filter_input(INPUT_POST, 'price');
    
    if ($category_id == NULL || $category_id == FALSE || $code == NULL || 
            $name == NULL || $price == NULL || $price == FALSE) {
        $error = "Invalid product data. Check all fields and try again.";
        include('../errors/error.php');
    } else {
        $current_category = $categoryDB->getCategory($category_id);

        $product = new Product();
        $product->setCategory($current_category);
        $product->setCode($code);
        $product->setName($name);
        $product->setPrice($price);

        $productDB->addProduct($product);

        header("Location: .?category_id=$category_id");
    }
}
?>