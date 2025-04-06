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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Auth - BookEasy</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #F4F1EB, #B7BDB7);
      height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      color: #050315;
      overflow: hidden;
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
      margin-left: 20px;
    }

    .auth-container {
      margin-top: 4vh;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      padding: 40px;
      width: 360px;
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      text-align: center;
      color: #1E0D73;
      margin-bottom: 20px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 12px;
      font-size: 15px;
      background: #ffffffdd;
      transition: border-color 0.3s ease;
    }

    input:focus {
      border-color: #1E0D73;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #1E0D73;
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
      background-color: #15095d;
      transform: scale(1.03);
    }

    .switch-link {
      margin-top: 15px;
      text-align: center;
      font-size: 14px;
    }

    .switch-link a {
      color: #FF9800;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .switch-link a:hover {
      color: #e68500;
    }

    .error {
      color: red;
      text-align: center;
      margin-bottom: 15px;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <nav>
    <div class="logo">BookEasy</div>
  </nav>
  <div class="auth-container">
    <h2 id="form-title">Login</h2>
    <?php if ($error)
      echo '<p class="error">' . htmlspecialchars($error) . '</p>'; ?>
    <form method="POST" id="auth-form">
      <input type="hidden" name="action" value="login" id="form-action">

      <div id="name-field" style="display: none;">
        <input type="text" name="name" placeholder="Full Name">
      </div>

      <div id="phone-field" style="display: none;">
        <input type="text" name="phone" placeholder="Phone Number">
      </div>

      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Submit</button>
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

    toggleLink.addEventListener('click', () => {
      const isLogin = formAction.value === 'login';
      formAction.value = isLogin ? 'signup' : 'login';
      formTitle.textContent = isLogin ? 'Sign Up' : 'Login';
      nameField.style.display = isLogin ? 'block' : 'none';
      phoneField.style.display = isLogin ? 'block' : 'none';
      toggleLink.textContent = isLogin ? 'Already have an account? Login' : "Don't have an account? Sign up";
    });
  </script>
</body>

</html>