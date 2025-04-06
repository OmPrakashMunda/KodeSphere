<?php
include '../utils/php/db.php';
include '../utils/php/whatsapp_api.php';
session_start();

$stage = 'phone'; // stages: phone, otp, reset
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['phone'])) {
    // Step 1: User submitted phone
    $phone = $_POST['phone'];
    $_SESSION['reset_phone'] = $phone;

    $stmt = $conn->prepare("SELECT * FROM customers WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
      $otp = rand(100000, 999999);
      $_SESSION['reset_otp'] = $otp;

      // Save OTP
      $update = $conn->prepare("UPDATE customers SET otp = ? WHERE phone = ?");
      $update->bind_param("ss", $otp, $phone);
      $update->execute();

      sendWhatsAppMessage($phone, "Your OTP for BookEasy is: $otp");
      $message = "OTP sent to your WhatsApp.";
      $stage = 'otp';
    } else {
      $error = "No user found with that phone.";
    }
  } elseif (isset($_POST['otp'])) {
    // Step 2: Verify OTP
    $entered_otp = $_POST['otp'];
    $phone = $_SESSION['reset_phone'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['otp'] == $entered_otp) {
      $message = "OTP verified. Please enter your new password.";
      $stage = 'reset';
    } else {
      $error = "Incorrect OTP. Try again.";
      $stage = 'otp';
    }
  } elseif (isset($_POST['new_password'])) {
    // Step 3: Reset Password
    $phone = $_SESSION['reset_phone'];
    $new_password = $_POST['new_password'];
    $hashed = password_hash($new_password, PASSWORD_BCRYPT);

    $update = $conn->prepare("UPDATE customers SET password = ?, otp = NULL WHERE phone = ?");
    $update->bind_param("ss", $hashed, $phone);
    $update->execute();

    $message = "Password reset successful!";
    session_destroy();
    $stage = 'done';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - BookEasy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0; padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #F4F1EB, #B7BDB7);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh; color: #050315;
    }
    .container {
      background: rgba(255,255,255,0.25);
      backdrop-filter: blur(10px);
      padding: 40px 30px;
      max-width: 400px; width: 100%;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(31,38,135,0.2);
      border: 1px solid rgba(255,255,255,0.18);
      text-align: center;
    }
    h2 { margin-bottom: 24px; color: #1E0D73; font-size: 26px; }
    input[type="text"], input[type="password"] {
      width: 93%; padding: 14px;
      margin-bottom: 20px; border-radius: 12px;
      border: 1px solid #ccc; font-size: 16px;
    }
    button {
      width: 100%; padding: 14px;
      background-color: #1E0D73; color: white;
      border: none; border-radius: 12px;
      font-size: 16px; cursor: pointer;
    }
    button:hover { background-color: #15095d; }
    .message { margin-top: 15px; font-size: 14px; color: green; }
    .error { margin-top: 15px; font-size: 14px; color: red; }
    a.back-link {
      display: inline-block; margin-top: 25px;
      text-decoration: none; color: #FF9800;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Reset Password</h2>
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>

    <?php if ($stage === 'phone') : ?>
      <form method="POST">
        <input type="text" name="phone" placeholder="Enter phone with country code" required>
        <button type="submit">Send OTP</button>
      </form>
    <?php elseif ($stage === 'otp') : ?>
      <form method="POST">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify OTP</button>
      </form>
    <?php elseif ($stage === 'reset') : ?>
      <form method="POST">
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit">Reset Password</button>
      </form>
    <?php elseif ($stage === 'done') : ?>
      <a href="auth.php" class="back-link">← Back to Login</a>
    <?php endif; ?>
  </div>
</body>
</html>
