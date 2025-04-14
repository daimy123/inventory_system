<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $payment_id = $_GET['id'];

    // Fetch payment details
    $sql = "SELECT * FROM payments WHERE payment_id = $payment_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();
    } else {
        echo "Payment not found!";
        exit;
    }
} else {
    echo "Invalid Payment ID!";
    exit;
}

// Update the payment if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];

    $update_sql = "UPDATE payments SET amount = '$amount', payment_date = '$payment_date' WHERE payment_id = $payment_id";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: payments.php?message=Payment updated successfully");
    } else {
        echo "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1>Update Payment</h1>

    <!-- Update Payment Form -->
    <form method="POST">
        <div class="mb-3">
            <label for="amount" class="form-label">Payment Amount</label>
            <input type="number" class="form-control" id="amount" name="amount" value="<?php echo $payment['amount']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="payment_date" class="form-label">Payment Date</label>
            <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo $payment['payment_date']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Payment</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
