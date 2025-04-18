<?php
session_start();
if (!isset($_SESSION['business_logged_in'])) {
  echo "unauthorized";
  exit();
}

include '../utils/php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];

  $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "error";
  }

  $stmt->close();
  $conn->close();
} else {
  echo "invalid";
}
?>
