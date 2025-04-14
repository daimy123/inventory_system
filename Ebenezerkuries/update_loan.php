<?php
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch loan ID from query string
$loan_id = $_GET['id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update the loan
    $loan_amount = $_POST['loan_amount'];
    $interest_rate = $_POST['interest_rate'];
    $loan_term = $_POST['loan_term'];
    $payment_status = $_POST['payment_status'];
    $loan_date = $_POST['loan_date'];

    $update_sql = "UPDATE loans SET 
                    loan_amount='$loan_amount', 
                    interest_rate='$interest_rate',
                    loan_term='$loan_term',
                    payment_status='$payment_status',
                    loan_date='$loan_date'
                   WHERE loan_id='$loan_id'";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: loan.php?message=Loan updated successfully");
        exit;
    } else {
        echo "Error updating loan: " . $conn->error;
    }
}

// Fetch existing loan data
$loan_sql = "SELECT * FROM loans WHERE loan_id='$loan_id'";
$loan_result = $conn->query($loan_sql);
$loan = $loan_result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Loan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>Edit Loan - ID: <?= $loan_id ?></h2>

    <form method="POST">
        <div class="mb-3">
            <label>Loan Amount</label>
            <input type="number" name="loan_amount" class="form-control" value="<?= $loan['loan_amount'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Interest Rate</label>
            <input type="number" name="interest_rate" class="form-control" step="0.01" value="<?= $loan['interest_rate'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Loan Term (months)</label>
            <input type="number" name="loan_term" class="form-control" value="<?= $loan['loan_term'] ?>">
        </div>
        <div class="mb-3">
            <label>Payment Status</label>
            <select name="payment_status" class="form-control">
                <option value="Pending" <?= $loan['payment_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Ongoing" <?= $loan['payment_status'] == 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                <option value="Completed" <?= $loan['payment_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Loan Date</label>
            <input type="date" name="loan_date" class="form-control" value="<?= $loan['loan_date'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Loan</button>
        <a href="loans.php" class="btn btn-secondary">Cancel</a>
    </form>

</body>
</html>

<?php $conn->close(); ?>
