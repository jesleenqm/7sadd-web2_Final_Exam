<?php
$host = 'localhost';
$db = 'comshop';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Process form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $contactnumber = $_POST['contactnumber'] ?? '';
    $address = $_POST['address'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    // Check if username already exists
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, redirect to login page
        header("Location: login.php");
        exit();
    } else {
        // User does not exist, proceed with registration
        $sql = "INSERT INTO users (firstname, lastname, contactnumber, address, username, password) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssss", $firstname, $lastname, $contactnumber, $address, $username, $password);

        if ($stmt->execute()) {
            $message = "<p style='color: green;'>Registration successful! You will be redirected to the login page.</p>";
            // Redirect after 1 seconds
            header("Refresh: 1; url=login.php");
        } else {
            $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register Page</title>
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
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        border: 2px solid black;
        width: 350px;
        text-align: center;
        color: #333;
    }

    .login-box h2 {
      margin-bottom: 25px;
    }

    .input-box {
      margin-bottom: 15px;
    }

    .input-box input {
      width: 100%;
      padding: 10px;
      border: 1px solid black;
      border-radius: 8px;
      outline: none;
      background: #f8f9fa;
    }

    .login-box button {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 8px;
      background-color: green;
      color: white;
      font-size: 16px;
      cursor: pointer;
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
    <h2>Register</h2>
    <?php if (!empty($message)) echo $message; ?>
    <form action="" method="POST">
      <div class="input-box">
        <input type="text" name="firstname" placeholder="Firstname" required />
      </div>
      <div class="input-box">
        <input type="text" name="lastname" placeholder="Lastname" required />
      </div>
      <div class="input-box">
        <input type="text" name="contactnumber" placeholder="Contact Number" required />
      </div>
      <div class="input-box">
        <input type="address" name="address" placeholder="Address" required id="address" />
      </div>
      <div class="input-box">
        <input type="text" name="username" placeholder="Username" required />
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Password" required id="password" />
      </div>
      <button type="submit">Register</button>
      <a href="login.php">Already have an account? Login</a>
    </form>
  </div>
  <script src="C:\xampp\htdocs\ComShop\js\bootstrap.bundle.min.js"> </script>
</body>
</html>
