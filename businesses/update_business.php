<?php
session_start();
if (!isset($_SESSION['business_logged_in'])) {
    header('Location: login.php');
    exit();
}

include '../utils/php/db.php';

$business_email = $_SESSION['business_email'];
$stmt = $conn->prepare("SELECT * FROM businesses WHERE email = ?");
$stmt->bind_param("s", $business_email);
$stmt->execute();
$result = $stmt->get_result();
$business = $result->fetch_assoc();
$stmt->close();

$business_id = $business['id'];
$name = $_POST['name'];
$phone = $_POST['phone'];
$slots = $_POST['slots'];
$working_days = $_POST['working_days'];
$working_hours_start = $_POST['working_hours_start'];
$working_hours_end = $_POST['working_hours_end'];

$image_path = $business['image']; // default: existing image filename

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $allowed)) {
        $target_dir = __DIR__ . "/images/"; // absolute path
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true); // create if not exists
        }

        $filename = $business_id . "." . $ext;
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $filename; // Save only filename in DB
        }
    }
}

$update_stmt = $conn->prepare("UPDATE businesses SET business_name = ?, phone = ?, slots = ?, working_days = ?, working_hours_start = ?, working_hours_end = ?, image = ? WHERE id = ?");
$update_stmt->bind_param("sssssssi", $name, $phone, $slots, $working_days, $working_hours_start, $working_hours_end, $image_path, $business_id);
$update_stmt->execute();
$update_stmt->close();

header("Location: index.php");
exit();
?>
