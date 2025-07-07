<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'comshop';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Update session logs with logout time
    $update_logout_query = "UPDATE session_logs 
                            SET logout_time = NOW() 
                            WHERE user_id = $user_id AND logout_time IS NULL
                            ORDER BY id DESC LIMIT 1";
    $conn->query($update_logout_query);
}

// Destroy the session and log the user out
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();
?>
