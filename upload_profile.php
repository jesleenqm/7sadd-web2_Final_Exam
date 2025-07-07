<?php
session_start();

// Ensure that the user is logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'comshop';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_picture'];

    // Define allowed file types
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $file['type'];

    // Check if file is valid
    if (in_array($file_type, $allowed_types)) {
        $filename = time() . "_" . $file['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . $filename;

        // Move uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update the profile picture in the database
            $update_stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $update_stmt->bind_param("si", $filename, $user_id);
            if ($update_stmt->execute()) {
                $_SESSION['message'] = "Profile picture updated successfully!";
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['message'] = "Error updating profile picture.";
            }
        } else {
            $_SESSION['message'] = "Failed to upload the file.";
        }
    } else {
        $_SESSION['message'] = "Invalid file type. Only images are allowed.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload Profile Picture</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Upload Profile Picture</h2>

  <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
  <?php endif; ?>

  <form action="upload_profile.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="profile_picture" class="form-label">Choose a Profile Picture</label>
      <input type="file" class="form-control" id="profile_picture" name="profile_picture" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
  </form>

  <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
