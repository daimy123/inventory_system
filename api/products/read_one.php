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

// Check if ID parameter exists
if(!isset($_GET['id'])) {
    Response::error("Missing ID parameter.", 400);
    exit;
}

// Get ID from URL
$id = htmlspecialchars(strip_tags($_GET['id']));

try {
    // Query to read single product
    $query = "SELECT id, name, description, category, quantity, price, created_at, updated_at
              FROM products
              WHERE id = ?
              LIMIT 0,1";
    
    // Prepare statement
    $stmt = $db->prepare($query);
    
    // Bind ID
    $stmt->bindParam(1, $id);
    
    // Execute query
    $stmt->execute();
    
    // Check if product exists
    if($stmt->rowCount() > 0){
        // Get record details
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Create product array
        $product = [
            "id" => $row["id"],
            "name" => $row["name"],
            "description" => $row["description"],
            "category" => $row["category"],
            "quantity" => $row["quantity"],
            "price" => $row["price"],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
        
        Response::success("Product found.", $product);
    } else {
        Response::error("Product not found.", 404);
    }
} catch(PDOException $e) {
    Response::error("Database error: " . $e->getMessage(), 500);
}
?>