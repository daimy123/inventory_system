<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $payment_id = $_GET['id'];

    // Delete payment from the database
    $delete_sql = "DELETE FROM payments WHERE payment_id = $payment_id";

    if ($conn->query($delete_sql) === TRUE) {
        header("Location: payments.php?message=Payment deleted successfully");
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid Payment ID!";
    exit;
}

$conn->close();
?>
