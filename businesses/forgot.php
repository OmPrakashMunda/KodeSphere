<?php
session_start();
if (isset($_SESSION['business_logged_in']) && $_SESSION['business_logged_in'] === true) {
  header('Location: index.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password - BookEasy</title>
  <link rel="stylesheet" href="main.css"/>
  <style>
    .auth-page {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: linear-gradient(to right, #F4F1EB, #B7BDB7);
      font-family: 'Poppins', sans-serif;
    }
    .auth-card {
      background: rgba(255, 255, 255, 0.3);
      padding: 40px 30px;
      border-radius: 20px;
      max-width: 400px;
      width: 100%;
      box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
      backdrop-filter: blur(10px);
    }
    .auth-form {
      display: flex;
      flex-direction: column;
    }
    .auth-form .input-field {
      margin-bottom: 20px;
    }
    .auth-form label {
      margin-bottom: 8px;
      font-weight: 500;
    }
    .auth-form input {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 16px;
    }
    .auth-btn {
      padding: 12px;
      background-color: #1E0D73;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
    }
    .auth-btn:hover {
      background-color: #15095d;
    }
    .auth-links {
      margin-top: 15px;
      text-align: center;
    }
    .auth-links a {
      color: #FF9800;
      text-decoration: none;
      font-weight: bold;
    }
    .auth-links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="auth-page">
    <div class="auth-card">
      <h2>Forgot Password</h2>
      <form class="auth-form" method="POST" action="reset-password.php">
        <div class="input-field">
          <label for="phone">Enter your registered phone number</label>
          <input type="text" id="phone" name="phone" placeholder="e.g. 9876543210" required />
        </div>
        <button type="submit" class="auth-btn">Send OTP</button>
        <div class="auth-links">
          <p>Remembered? <a href="login.php">Back to Login</a></p>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
