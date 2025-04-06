<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../utils/php/db.php';
session_start();

if (!isset($_SESSION['customer_logged_in'])) {
  header('Location: index.php');
  exit();
}

$customer_email = $_SESSION['customer_email'];
$customer_name = '';
$customer_phone = '';

// Get logged-in customer's details
$stmt_user = $conn->prepare("SELECT name, phone FROM customers WHERE email = ?");
$stmt_user->bind_param("s", $customer_email);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
if ($user_result && $user_result->num_rows > 0) {
  $user = $user_result->fetch_assoc();
  $customer_name = $user['name'];
  $customer_phone = $user['phone'];
}
$stmt_user->close();

$business_id = $_GET['business_id'] ?? null;
$success = false;
$booking_code = '';
$business = null;

if ($business_id) {
  $stmt = $conn->prepare("SELECT * FROM businesses WHERE id = ?");
  $stmt->bind_param("i", $business_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $business = $result->fetch_assoc();
  $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $business_id = $_POST['business_id'];
  $slot_time = $_POST['slot_time'];
  $service = $_POST['service'];
  $notes = $_POST['notes'];
  $created_at = date('Y-m-d H:i:s');
  $booking_code = uniqid('BOOK');

  $stmt = $conn->prepare("INSERT INTO bookings (booking_code, business_id, customer_name, customer_phone, customer_email, slot_time, service, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
  }

  $stmt->bind_param("sisssssss", $booking_code, $business_id, $customer_name, $customer_phone, $customer_email, $slot_time, $service, $notes, $created_at);

  if ($stmt->execute()) {
    $success = true;

    // Fetch business phone number - FIX: Proper parameterized query
    $stmt2 = $conn->prepare("SELECT business_name, phone FROM businesses WHERE id = ?");
    $stmt2->bind_param("i", $business_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $bizData = $res2->fetch_assoc();
    $stmt2->close();

    $biz_phone = $bizData['phone'];
    $biz_name = $bizData['business_name']; // Fixed variable name to match the SELECT statement

    // Format WhatsApp message
    $message = "📢 New Booking on BookEasy!\n\n"
      . "👤 Customer: $customer_name\n"
      . "📞 Phone: $customer_phone\n"
      . "📧 Email: $customer_email\n"
      . "🗓️ Slot: $slot_time\n"
      . "🛎️ Service: $service\n"
      . "📝 Notes: $notes\n"
      . "🔐 Booking Code: $booking_code\n\n"
      . "Please prepare accordingly.";

    // Send WhatsApp message
    include_once '../utils/php/whatsapp_api.php';
    sendWhatsAppMessage($biz_phone, $message);
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Book Appointment</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      padding: 0;
      margin: 0;
      background-color: #f9f9f9;
      color: #333;
    }

    h1 {
      color: #1E0D73;
      text-align: center;
      margin-bottom: 10px;
    }

    .business-card {
      max-width: 600px;
      margin: 0 auto 20px;
      padding: 20px;
      background: #fff;
      border-left: 6px solid #1E0D73;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.06);
    }

    .business-card h2 {
      margin: 0;
      color: #1E0D73;
    }

    .business-card p {
      margin: 8px 0;
      font-size: 14px;
    }

    form {
      max-width: 500px;
      margin: auto;
      background: white;
      padding: 24px;
      border: 1px solid #ddd;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    input,
    textarea,
    select {
      width: 95%;
      margin-bottom: 12px;
      padding: 10px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    input[readonly] {
      background-color: #eee;
    }

    button {
      padding: 10px 16px;
      background-color: #FF9800;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 16px;
      width: 100%;
    }

    .success {
      text-align: center;
      margin-top: 30px;
    }

    .success h2 {
      color: #1E0D73;
      margin-bottom: 12px;
    }

    .success img {
      margin-top: 12px;
      border: 2px solid #1E0D73;
      padding: 6px;
      background: white;
    }

    .navbar {
      background-color: #1E0D73;
      color: white;
      padding: 12px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar .logo {
      font-weight: bold;
      font-size: 20px;
      letter-spacing: 1px;
    }

    .navbar a.logout-btn {
      background-color: white;
      color: #FF9800;
      padding: 8px 16px;
      text-decoration: none;
      border-radius: 4px;
      font-weight: 500;
      transition: 0.2s;
    }

    .navbar a.logout-btn:hover {
      background-color: #eee;
    }
  </style>
</head>

<body>
  <div class="navbar">
    <div class="logo">BookEasy</div>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>

  <h1>Book an Appointment</h1>

  <?php if ($business): ?>
    <div class="business-card">
      <h2><?= htmlspecialchars($business['business_name']) ?></h2>
      <?php if (!empty($business['description'])): ?>
        <p><?= nl2br(htmlspecialchars($business['description'])) ?></p>
      <?php endif; ?>
      <?php if (!empty($business['contact'])): ?>
        <p><strong>Contact:</strong> <?= htmlspecialchars($business['contact']) ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success">
      <h2>✅ Booking Successful!</h2>
      <p><strong>Your Booking Code:</strong></p>
      <p><?= htmlspecialchars($booking_code) ?></p>
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($booking_code) ?>" alt="QR Code for Booking">
    </div>
  <?php else: ?>
    <form method="POST">
      <input type="hidden" name="business_id" value="<?= htmlspecialchars($business_id) ?>" required>
      <input type="text" name="customer_name" value="<?= htmlspecialchars($customer_name) ?>" placeholder="Your Name" readonly>
      <input type="text" name="customer_phone" value="<?= htmlspecialchars($customer_phone) ?>" placeholder="Phone Number" readonly>
      <input type="email" name="customer_email" value="<?= htmlspecialchars($customer_email) ?>" placeholder="Email Address" readonly>
      <input type="datetime-local" name="slot_time" required>
      <input type="text" name="service" placeholder="Service (e.g., Haircut)" required>
      <textarea name="notes" placeholder="Any notes..."></textarea>
      <button type="submit">Book Appointment</button>
    </form>
  <?php endif; ?>
</body>
</html>