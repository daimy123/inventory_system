<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and response utility
include_once '../config/database.php';
include_once '../utils/response.php';

// Database connection
$database = new Database();
$db = $database->getConnection();

try {
    // Create query
    $query = "SELECT id, name, description, category, quantity, price, created_at, updated_at FROM products ORDER BY id DESC";
    
    // Prepare statement
    $stmt = $db->prepare($query);
    
    // Execute query
    $stmt->execute();
    
    // Get number of rows
    $num = $stmt->rowCount();
    
    // Check if more than 0 records found
    if($num > 0){
        // Products array
        $products_arr = [];
        
        // Retrieve results
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $product_item = [
                "id" => $row["id"],
                "name" => $row["name"],
                "description" => $row["description"],
                "category" => $row["category"],
                "quantity" => $row["quantity"],
                "price" => $row["price"],
                "created_at" => $row["created_at"],
                "updated_at" => $row["updated_at"]
            ];
            
            array_push($products_arr, $product_item);
        }
        
        Response::success("Products retrieved successfully.", $products_arr);
    } else {
        Response::success("No products found.", []);
    }
} catch(PDOException $e) {
    Response::error("Database error: " . $e->getMessage(), 500);
}
?>