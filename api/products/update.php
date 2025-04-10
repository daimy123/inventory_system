<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and response utility
include_once '../config/database.php';
include_once '../utils/response.php';

// Database connection
$database = new Database();
$db = $database->getConnection();

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty and ID is set
if(!isset($data->id) || empty($data->id)) {
    Response::error("Missing product ID.", 400);
    exit;
}

try {
    // First, check if product exists
    $check_query = "SELECT id FROM products WHERE id = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(1, $data->id);
    $check_stmt->execute();
    
    if($check_stmt->rowCount() == 0) {
        Response::error("Product not found.", 404);
        exit;
    }
    
    // Prepare update query based on which fields are provided
    $update_query = "UPDATE products SET ";
    $params = [];
    
    // Check each field and add to query if present
    if(isset($data->name) && !empty($data->name)) {
        $update_query .= "name=:name, ";
        $params[':name'] = htmlspecialchars(strip_tags($data->name));
    }
    
    if(isset($data->description)) {
        $update_query .= "description=:description, ";
        $params[':description'] = htmlspecialchars(strip_tags($data->description));
    }
    
    if(isset($data->category) && !empty($data->category)) {
        $update_query .= "category=:category, ";
        $params[':category'] = htmlspecialchars(strip_tags($data->category));
    }
    
    if(isset($data->quantity)) {
        $update_query .= "quantity=:quantity, ";
        $params[':quantity'] = htmlspecialchars(strip_tags($data->quantity));
    }
    
    if(isset($data->price)) {
        $update_query .= "price=:price, ";
        $params[':price'] = htmlspecialchars(strip_tags($data->price));
    }
    
    // Remove trailing comma and space
    $update_query = rtrim($update_query, ", ");
    
    // Add WHERE clause
    $update_query .= " WHERE id=:id";
    $params[':id'] = $data->id;
    
    // If no fields to update
    if(count($params) <= 1) {
        Response::error("No fields to update.", 400);
        exit;
    }
    
    // Prepare and execute query
    $stmt = $db->prepare($update_query);
    
    // Execute query
    if($stmt->execute($params)){
        Response::success("Product was updated successfully.");
    } else {
        Response::error("Unable to update product.", 503);
    }
} catch(PDOException $e) {
    Response::error("Database error: " . $e->getMessage(), 500);
}
?>