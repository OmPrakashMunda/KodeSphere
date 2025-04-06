<?php
session_start();
include '../utils/php/db.php';

$fullName = $_POST['full_name'];
$businessName = $_POST['business_name'];
$businessType = $_POST['business_type'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO businesses (full_name, business_name, business_type, email, password) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $fullName, $businessName, $businessType, $email, $password);

if ($stmt->execute()) {
  $_SESSION['business_logged_in'] = true;
  $_SESSION['business_email'] = $email;
  header("Location: index.php");
} else {
  echo "Error: " . $stmt->error;
}
?>
