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


if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists and has not expired
    $result = $conn->query("SELECT id, reset_token_expiry FROM users WHERE reset_token='$token'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $expiry = $row['reset_token_expiry'];

        // Check if the token has expired
        if (strtotime($expiry) > time()) {
            // Token is valid, allow user to reset their password
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Hash the new password

                // Update the password in the database and remove the reset token
                $conn->query("UPDATE users SET password='$new_password', reset_token=NULL, reset_token_expiry=NULL WHERE reset_token='$token'");

                $_SESSION['message'] = "Your password has been reset successfully.";
                header("Location: login.php");  // Redirect to the login page after successful password reset
                exit();
            }
        } else {
            $_SESSION['message'] = "The reset token has expired.";
        }
    } else {
        $_SESSION['message'] = "Invalid token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; unset($_SESSION['message']); } ?>
    
    <form method="POST">
        <label>New Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>

