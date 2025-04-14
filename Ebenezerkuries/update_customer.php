<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the customer ID is provided
if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Fetch the existing customer data
    $sql = "SELECT * FROM customers WHERE customer_id = $customer_id";
    $result = $conn->query($sql);
    $customer = $result->fetch_assoc();
}

// Update customer data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];
    $address = $_POST['address'];

    // Update the customer information
    $update_sql = "UPDATE customers SET name='$name', contact_info='$contact_info', address='$address' WHERE customer_id=$customer_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "Customer updated successfully";
        header("Location: index.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1 class="text-center">Update Customer</h1>

    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $customer['name']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="contact_info" class="form-label">Contact Information</label>
            <input type="text" class="form-control" id="contact_info" name="contact_info" value="<?php echo $customer['contact_info']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $customer['address']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Customer</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
