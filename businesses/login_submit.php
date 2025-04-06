<?php
session_start();
include '../utils/php/db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM businesses WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();
  if (password_verify($password, $user['password'])) {
    $_SESSION['business_logged_in'] = true;
    $_SESSION['business_email'] = $user['email'];
    header("Location: index.php");
    exit;
  } else {
    echo "Incorrect password.";
  }
} else {
  echo "No account found with that email.";
}
?>
