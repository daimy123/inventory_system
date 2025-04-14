<?php
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch loan data for dropdown
$sql = "SELECT loan_id, loan_amount FROM loans";
$loan_result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_id = $_POST['loan_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];

    // Insert the payment into the database
    $insert_sql = "INSERT INTO payments (loan_id, amount, payment_date) 
                   VALUES ('$loan_id', '$amount', '$payment_date')";
    
    if ($conn->query($insert_sql) === TRUE) {
        header("Location: payments.php?message=Payment added successfully");
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
    <title>Ebenezer Kuries - Add Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Sticky Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Ebenezer Kuries</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="loans.php">Loans</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Payments</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="text-center">Add Payment</h1>

    <form method="POST" action="add_payment.php">
        <div class="mb-3">
            <label for="loan_id" class="form-label">Select Loan</label>
            <select class="form-control" id="loan_id" name="loan_id" required>
                <option value="">Select Loan</option>
                <?php
                if ($loan_result->num_rows > 0) {
                    while ($row = $loan_result->fetch_assoc()) {
                        echo "<option value='" . $row['loan_id'] . "'>" . "Loan ID: " . $row['loan_id'] . " - Amount: " . number_format($row['loan_amount'], 2) . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Payment Amount</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>

        <div class="mb-3">
            <label for="payment_date" class="form-label">Payment Date</label>
            <input type="date" class="form-control" id="payment_date" name="payment_date" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Payment</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
$conn->close();
?>
