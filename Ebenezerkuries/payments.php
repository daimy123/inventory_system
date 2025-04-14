<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the list of payments with loan details
$sql = "SELECT payments.payment_id, payments.loan_id, payments.amount, payments.payment_date, loans.loan_amount
        FROM payments
        INNER JOIN loans ON payments.loan_id = loans.loan_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ebenezer Kuries - Payments Management</title>
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
                    <a class="nav-link" href="loan.php">Loans</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Payments</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="text-center">Payments Management</h1>

    <!-- Add Payment Button -->
    <div class="mt-3">
        <a href="add_payment.php" class="btn btn-success">Add Payment</a>
    </div>

    <!-- List of Payments -->
    <div class="mt-5">
        <h3>Payment List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Loan ID</th>
                    <th>Loan Amount</th>
                    <th>Payment Amount</th>
                    <th>Payment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["payment_id"] . "</td>
                                <td>" . $row["loan_id"] . "</td>
                                <td>" . number_format($row["loan_amount"], 2) . "</td>
                                <td>" . number_format($row["amount"], 2) . "</td>
                                <td>" . $row["payment_date"] . "</td>
                                <td>
                                    <a href='edit_payment.php?id=" . $row["payment_id"] . "' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_payment.php?id=" . $row["payment_id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No payments found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
