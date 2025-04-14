<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];
    $address = $_POST['address'];

    // Insert the customer data into the database
    $sql = "INSERT INTO customers (name, contact_info, address) VALUES ('$name', '$contact_info', '$address')";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to index.php after successful insertion
        header("Location: index.php");
        exit(); // Ensure that no further code is executed after the redirect
    } else {
        // Show error message if insertion fails
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
