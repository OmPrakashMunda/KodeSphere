<?php
include '../utils/php/db.php';
session_start();

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action']) && $_POST['action'] === 'signup') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO customers (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $password);

    if ($stmt->execute()) {
      $_SESSION['customer_logged_in'] = true;
      $_SESSION['customer_email'] = $email;
      header('Location: index.php');
      exit();
    } else {
      $error = 'Signup failed. Try again.';
    }
    $stmt->close();

  } elseif (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['customer_logged_in'] = true;
      $_SESSION['customer_email'] = $email;
      header('Location: index.php');
      exit();
    } else {
      $error = 'Invalid login credentials.';
    }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Auth - BookEasy</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: #f4f4f4;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    nav {
      background-color: #1E0D73;
      padding: 1rem 2rem;
      width: 100%;
      color: white;
      font-weight: 600;
      font-size: 1.2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .logo {
      font-size: 1.5rem;
      font-weight: 600;
      color: white;
    }

    .auth-container {
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      padding: 40px 30px;
      text-align: center;
      margin-top: 5vh;
      animation: fadeIn 0.7s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      color: #1E0D73;
      font-size: 24px;
      margin-bottom: 30px;
      line-height: 1.4;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 14px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 15px;
      background: #fdfdfd;
      transition: border 0.3s ease;
    }

    input:focus {
      border-color: #1E0D73;
      outline: none;
    }

    button {
      width: 100%;
      padding: 14px;
      background-color: #1E0D73;
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 20px;
      transition: all 0.3s ease;
    }

    button:hover {
      background-color: #15095d;
      transform: translateY(-2px);
    }

    .switch-link {
      margin-top: 20px;
      font-size: 14px;
      color: #1E0D73;
    }

    .switch-link a {
      color: #1E0D73;
      font-weight: 600;
      text-decoration: none;
    }

    .switch-link a:hover {
      text-decoration: underline;
    }

    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

  <nav>
    <div class="logo">BookEasy</div>
  </nav>

  <div class="auth-container">
    <h2 id="form-title">Login to Your Account</h2>
    <?php if ($error) echo '<p class="error">' . htmlspecialchars($error) . '</p>'; ?>
    
    <form method="POST" id="auth-form">
      <input type="hidden" name="action" value="login" id="form-action">

      <div id="name-field" style="display: none;">
        <input type="text" name="name" placeholder="Full Name">
      </div>

      <div id="phone-field" style="display: none;">
        <input type="text" name="phone" placeholder="Phone Number">
      </div>

      <input type="email" name="email" placeholder="Email Address" required>
      <input type="password" name="password" placeholder="Password" required>

      <button type="submit">Login</button>

      <div class="switch-link">
        <a href="#" id="toggle-form">Don't have an account? Sign up</a><br>
        <a href="forgot_password.php">Forgot Password?</a>
      </div>
    </form>
  </div>

  <script>
    const toggleLink = document.getElementById('toggle-form');
    const formTitle = document.getElementById('form-title');
    const formAction = document.getElementById('form-action');
    const nameField = document.getElementById('name-field');
    const phoneField = document.getElementById('phone-field');
    const submitButton = document.querySelector("button");

    toggleLink.addEventListener('click', (e) => {
      e.preventDefault();
      const isLogin = formAction.value === 'login';
      formAction.value = isLogin ? 'signup' : 'login';
      formTitle.innerHTML = isLogin ? 'Create Your Account' : 'Login to Your Account';
      nameField.style.display = isLogin ? 'block' : 'none';
      phoneField.style.display = isLogin ? 'block' : 'none';
      submitButton.textContent = isLogin ? 'Sign Up' : 'Login';
      toggleLink.innerHTML = isLogin ? 'Already have an account? Login' : "Don't have an account? Sign up";
    });
  </script>

</body>
</html>
