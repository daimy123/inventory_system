<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the customer ID is provided
if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Delete the customer from the database
    $sql = "DELETE FROM customers WHERE customer_id = $customer_id";

    if ($conn->query($sql) === TRUE) {
        echo "Customer deleted successfully";
        header("Location: index.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
