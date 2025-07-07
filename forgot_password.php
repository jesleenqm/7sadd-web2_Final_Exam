<?php
session_start();
$host = 'localhost';
$db = 'comshop';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];

    // Check if the username exists in the database
    $result = $conn->query("SELECT id FROM users WHERE username='$username'");
    if ($result->num_rows > 0) {
        // Generate a unique reset token and set expiry time (1 hour from now)
        $token = bin2hex(random_bytes(16));  // Generate a random token
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));  // Set expiry for 1 hour

        // Update the user with the generated reset token and expiry
        $conn->query("UPDATE users SET reset_token='$token', reset_token_expiry='$expiry' WHERE username='$username'");

        // Redirect to the reset password page with the token
        header("Location: reset_password.php?token=$token");
        exit();
    } else {
        $_SESSION['message'] = "No account found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; unset($_SESSION['message']); } ?>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
