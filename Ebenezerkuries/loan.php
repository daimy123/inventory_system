<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the list of loans with customer names
$sql = "SELECT loans.loan_id, loans.loan_amount, loans.interest_rate, loans.loan_date, customers.name as customer_name 
        FROM loans 
        INNER JOIN customers ON loans.customer_id = customers.customer_id";
$result = $conn->query($sql);

// Fetch customer names for the Add Loan form
$customer_sql = "SELECT customer_id, name FROM customers";
$customer_result = $conn->query($customer_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ebenezer Kuries - Loan Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 0;
        }
    </style>
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
                    <a class="nav-link active" href="#">Loans</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="payments.php">Payments</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="text-center">Ebenezer Kuries - Loan Management</h1>

    <!-- Add Loan Form -->
<div class="mb-3">
    <h3>Add New Loan</h3>
    <form action="add_loan.php" method="POST">
        <div class="mb-3">
            <label for="customer_id" class="form-label">Customer Name</label>
            <select class="form-control" id="customer_id" name="customer_id" required>
                <option value="">Select Customer</option>
                <?php
                if ($customer_result->num_rows > 0) {
                    while ($row = $customer_result->fetch_assoc()) {
                        echo "<option value='" . $row['customer_id'] . "'>" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No customers found</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="loan_amount" class="form-label">Loan Amount</label>
            <input type="number" class="form-control" id="loan_amount" name="loan_amount" required>
        </div>

        <div class="mb-3">
            <label for="interest_rate" class="form-label">Interest Rate</label>
            <input type="number" step="0.01" class="form-control" id="interest_rate" name="interest_rate" required>
        </div>

        <div class="mb-3">
            <label for="loan_term" class="form-label">Loan Term (in months)</label>
            <input type="number" class="form-control" id="loan_term" name="loan_term">
        </div>

        <div class="mb-3">
            <label for="payment_status" class="form-label">Payment Status</label>
            <select class="form-control" id="payment_status" name="payment_status">
                <option value="">Select Status</option>
                <option value="Pending">Pending</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="loan_date" class="form-label">Loan Date</label>
            <input type="date" class="form-control" id="loan_date" name="loan_date" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Loan</button>
    </form>
</div>


<!-- List of Loans -->
<div class="mt-5">
    <h3>Loan List</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Loan ID</th>
                <th>Customer Name</th>
                <th>Loan Amount</th>
                <th>Interest Rate</th>
                <th>Loan Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["loan_id"] . "</td>
                            <td>" . $row["customer_name"] . "</td>
                            <td>" . number_format($row["loan_amount"], 2) . "</td>
                            <td>" . number_format($row["interest_rate"], 2) . "%</td>
                            <td>" . $row["loan_date"] . "</td>
                            <td>
                                <a href='update_loan.php?id=" . $row["loan_id"] . "' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='delete_loan.php?id=" . $row["loan_id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No loans found</td></tr>";
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
