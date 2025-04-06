<?php
session_start();
if (isset($_SESSION['business_logged_in'])) {
  header("Location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register Your Business</title>
  <link rel="stylesheet" href="./main.css">
</head>
<body>
  <div class="auth-page">
    <div class="auth-card">
      <h2>Register Your Business</h2>
      <form class="auth-form" method="POST" action="register_submit.php">
        <div class="input-field">
          <label for="name">Full Name</label>
          <input type="text" name="full_name" id="full_name" required>
        </div>

        <div class="input-field">
          <label for="business">Business Name</label>
          <input type="text" name="business_name" id="business_name" required>
        </div>

        <div class="input-field">
          <label for="type">Business Type</label>
          <select name="business_type" id="business_type" required>
            <option value="">Select your business type</option>
            <option value="Gym">Gym</option>
            <option value="Cafe">Cafe</option>
            <option value="Banquet Hall">Banquet Hall</option>
            <option value="Parlour">Parlour</option>
            <option value="Spa">Spa</option>
            <option value="Massage">Massage</option>
            <option value="Saloon">Saloon</option>
            <option value="Repair">Repair</option>
          </select>
        </div>

        <div class="input-field">
          <label for="email">Email Address</label>
          <input type="email" name="email" id="email" required>
        </div>

        <div class="input-field">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" required>
        </div>

        <button class="auth-btn" type="submit">Register</button>
      </form>
      <div class="auth-links">
        Already registered? <a href="login.php">Login here</a>
      </div>
    </div>
  </div>
</body>
</html>
