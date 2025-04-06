<?php
$host = 'localhost';
$user = 'bookeasy';
$pass = 'LicNC4ZDyfMi2A4B';
$dbname = 'bookeasy';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
