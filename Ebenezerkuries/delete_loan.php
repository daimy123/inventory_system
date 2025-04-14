<?php
$conn = new mysqli("localhost", "root", "", "ebenezer_kuries");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loan_id = $_GET['id'] ?? null;

if ($loan_id) {
    $sql = "DELETE FROM loans WHERE loan_id='$loan_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: loan.php?message=Loan deleted successfully");
    } else {
        echo "Error deleting loan: " . $conn->error;
    }
} else {
    echo "No loan ID provided.";
}

$conn->close();
?>
