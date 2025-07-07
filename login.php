<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db = 'comshop';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? 'user';
        $_SESSION['logged_in'] = true;

        $log_stmt = $conn->prepare("INSERT INTO session_logs (user_id, login_time) VALUES (?, NOW())");
        $log_stmt->bind_param("i", $user['id']);
        $log_stmt->execute();
        $log_stmt->close();

        // Redirect based on role
        if ($_SESSION['role'] === 'admin') {
            header("Location: dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        $message = "<div class='alert alert-danger'>Invalid username or password.</div>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login Page</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      background: url(bg.jpg) no-repeat center center/cover;
      font-family: Arial, sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-box {
      background: white;
      padding: 50px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      border: 2px solid black;
      width: 450px;
      height: auto;
      text-align: center;
      color: #333;
    }
    .login-box h2 {
      margin-bottom: 25px;
    }
    .login-box a {
      display: block;
      margin-top: 15px;
      color: #007bff;
      text-decoration: none;
      font-size: 14px;
    }
    .login-box a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="login-box">
    <h2>Login</h2>
    <?php if (!empty($message)) echo $message; ?>
    <form action="" method="POST">
      <div class="mb-3">
        <input type="text" class="form-control" name="username" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" name="password" placeholder="Password" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-success">Login</button>
      </div>
      <div class="mt-2">
        <a href="forgot_password.php">Forgot Password?</a>
      </div>
      <a href="register.php">Don't have an account? Register</a>
    </form>
  </div>

  <script src="C:\xampp\htdocs\ComShop\js\bootstrap.bundle.min.js"> </script>
</body>
</html>
