<?php
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$customer_id = $_POST['customer_id'];
$loan_amount = $_POST['loan_amount'];
$interest_rate = $_POST['interest_rate'];
$loan_term = $_POST['loan_term'];
$payment_status = $_POST['payment_status'];
$loan_date = $_POST['loan_date'];

$sql = "INSERT INTO loans (customer_id, loan_amount, interest_rate, loan_term, payment_status, loan_date)
        VALUES ('$customer_id', '$loan_amount', '$interest_rate', '$loan_term', '$payment_status', '$loan_date')";

if ($conn->query($sql) === TRUE) {
    header("Location: loans.php?message=Loan added successfully");
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
