<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and response utility
include_once '../config/database.php';
include_once '../utils/response.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if(
    !empty($data->name) &&
    !empty($data->description) &&
    !empty($data->category) &&
    !empty($data->quantity) &&
    !empty($data->price)
){
    try {
        // Create query
        $query = "INSERT INTO products
                SET name=:name, description=:description, category=:category, quantity=:quantity, price=:price";
    
        // Prepare query
        $stmt = $db->prepare($query);
    
        // Sanitize data
        $name = htmlspecialchars(strip_tags($data->name));
        $description = htmlspecialchars(strip_tags($data->description));
        $category = htmlspecialchars(strip_tags($data->category));
        $quantity = htmlspecialchars(strip_tags($data->quantity));
        $price = htmlspecialchars(strip_tags($data->price));
    
        // Bind values
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":category", $category);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":price", $price);
    
        // Execute query
        if($stmt->execute()){
            // Create was successful
            Response::success("Product was created successfully.", ["id" => $db->lastInsertId()]);
        } else {
            // Failed to create
            Response::error("Unable to create product.", 503);
        }
    } catch(PDOException $e) {
        Response::error("Database error: " . $e->getMessage(), 500);
    }
}
// Data incomplete
else {
    Response::error("Unable to create product. Data is incomplete.", 400);
}
?>