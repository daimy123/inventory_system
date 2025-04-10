<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and response utility
include_once '../config/database.php';
include_once '../utils/response.php';

// Database connection
$database = new Database();
$db = $database->getConnection();

// Get product id
$data = json_decode(file_get_contents("php://input"));

// Check if ID is set
if(!isset($data->id) || empty($data->id)) {
    Response::error("Missing product ID.", 400);
    exit;
}

try {
    // Create query
    $query = "DELETE FROM products WHERE id = ?";
    
    // Prepare statement
    $stmt = $db->prepare($query);
    
    // Sanitize
    $id = htmlspecialchars(strip_tags($data->id));
    
    // Bind id
    $stmt->bindParam(1, $id);
    
    // Execute query
    if($stmt->execute()){
        // Check if any row was affected
        if($stmt->rowCount() > 0) {
            Response::success("Product was deleted successfully.");
        } else {
            Response::error("Product not found.", 404);
        }
    } else {
        Response::error("Unable to delete product.", 503);
    }
} catch(PDOException $e) {
    Response::error("Database error: " . $e->getMessage(), 500);
}
?>