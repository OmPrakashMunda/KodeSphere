<?php
session_start();
if (isset($_SESSION['business_logged_in'])) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Business Login</title>
  <link rel="stylesheet" href="main.css">
</head>
<body>
  <div class="auth-page">
    <div class="auth-card">
      <h2>Login to Your Business Account</h2>
      <form class="auth-form" method="POST" action="login_submit.php">
        
        <div class="input-field">
          <label for="email">Email Address</label>
          <input type="email" name="email" id="email" required>
        </div>

        <div class="input-field">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" required>
        </div>

        <button class="auth-btn" type="submit">Login</button>
      </form>

      <div class="auth-links">
        <a href="forgot.php">Forgot Password?</a><br>
        New here? <a href="register.php">Register your business</a>
      </div>
    </div>
  </div>
</body>
</html>
