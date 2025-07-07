<?php
session_start();

// Ensure that the user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
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

// Fetch admin data
$admin_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch profile picture
$profile_query = $conn->query("SELECT profile_picture FROM users WHERE id = $admin_id");
$profile_data = $profile_query->fetch_assoc();
$profile_picture = $profile_data['profile_picture'] ?? 'default.png';

// Update admin profile if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = $_POST['firstname'] ?? $user['firstname'];
    $lastname = $_POST['lastname'] ?? $user['lastname'];
    $contactnumber = $_POST['contactnumber'] ?? $user['contactnumber'];
    $address = $_POST['address'] ?? $user['address'];

    $update_stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, contactnumber = ?, address= ? WHERE id = ?");
    $update_stmt->bind_param("ssssi", $firstname, $lastname, $contactnumber, $address, $admin_id);

    if ($update_stmt->execute()) {
    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['message'] = "<div class='alert alert-success'>Profile updated successfully!</div>";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
    } else {
    $_SESSION['message'] = "<div class='alert alert-danger'>Error updating profile.</div>";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Profile</title>
  <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  <link rel="manifest" href="site.webmanifest">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }
    body {
      background: #f0f0f0;
    }
    .container {
      display: flex;
      height: 100vh;
    }
    .sidebar {
      width: 250px;
      background: url(bg.jpg) no-repeat center center/cover;
      color: white;
      padding: 20px;
      display: flex;
      flex-direction: column;
    }
    .sidebar h2 {
      margin-bottom: 20px;
      font-size: 22px;
      text-align: center;
    }
    .sidebar ul {
      list-style: none;
      padding: 0;
    }
    .sidebar ul li {
      margin: 10px 0;
      text-align: center;
    }
    .sidebar ul li a {
      background: white;
      color: black;
      text-decoration: none;
      font-size: 16px;
      display: block;
      padding: 12px;
      border-radius: 10px;
      transition: background 0.3s, color 0.3s;
    }
    .sidebar ul li a:hover {
      background: url(bg.jpg) no-repeat center center/cover;
      color: white;
    }
    .logout-btn {
      margin-top: auto;
      background: red;
      border: none;
      color: white;
      padding: 12px;
      font-size: 16px;
      border-radius: 10px;
      cursor: pointer;
      width: 100%;
      text-decoration: none;
      text-align: center;
    }
    .logout-btn:hover {
      background: darkred;
    }
    .main {
      flex-grow: 1;
      padding: 40px;
      overflow-y: auto;
    }
    .profile-header {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 20px;
      justify-content: space-between;
    }
    .profile-left {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .profile-icon {
      width: 120px;
      height: 120px;
    }
    .about-section {
      background: rgba(255, 255, 255, 0.8);
      padding: 20px;
      border-radius: 15px;
    }
    .about-section h3 {
      margin-bottom: 15px;
    }
    .info p {
      margin: 10px 0;
      padding: 8px;
      border-bottom: 1px solid #ccc;
    }
    .info input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      display: none;
    }
    .edit-btn, .save-btn {
      padding: 8px 16px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .edit-btn {
      background-color: #007bff;
      color: white;
    }
    .save-btn {
      background-color: #28a745;
      color: white;
      margin-top: 20px;
      display: none;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="dashboard.php">My Profile</a></li>
      <li><a href="hourly_rate.php">Hourly Rate</a></li>
      <li><a href="report.php">Reports</a></li>
      <li><a href="Register.php">Register a New User</a></li>
    </ul>
    <a href="Logout.php" class="logout-btn">Logout</a>
  </div>

  <div class="main">
    <div class="profile-header">
      <div class="profile-left d-flex align-items-center">
    <div class="upload-form text-center">
        <img src="uploads/<?= htmlspecialchars($profile_picture) ?>" class="profile-picture rounded-circle mb-2" alt="Profile Picture" style="width: 120px; height: 120px; object-fit: cover;">
    </div>
    <div class="ms-3">
        <h2 class="mt-3"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h2>
        <a href="upload_profile.php" class="btn btn-sm btn-primary mt-2">Upload Profile Picture</a>
    </div>
  </div>
      <button class="edit-btn" onclick="toggleEdit(true)">Edit</button>
    </div>

    <?php if (isset($message)): ?>
    <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <div class="about-section">
      <h3>About</h3>
      <form method="POST">
        <div class="info">
          <p><strong>First Name:</strong>
            <span id="firstNameDisplay"><?= $user['firstname'] ?></span>
            <input type="text" id="firstNameInput" name="firstname" value="<?= $user['firstname'] ?>" required>
          </p>
          <p><strong>Last Name:</strong>
            <span id="lastNameDisplay"><?= $user['lastname'] ?></span>
            <input type="text" id="lastNameInput" name="lastname" value="<?= $user['lastname'] ?>" required>
          </p>
          <p><strong>Username:</strong>
            <span id="usernameDisplay"><?= $user['username'] ?></span>
            <input type="text" id="usernameInput" name="username" value="<?= $user['username'] ?>" required>
          </p>
          <p><strong>Contact #:</strong>
            <span id="contactDisplay"><?= $user['contactnumber'] ?></span>
            <input type="text" id="contactInput" name="contactnumber" value="<?= $user['contactnumber'] ?>" required>
          </p>
          <p><strong>Address:</strong>
            <span id="addressDisplay"><?= $user['address'] ?></span>
            <input type="text" id="addressInput" name="address" value="<?= $user['address'] ?>" required>
          </p>
        </div>
        <button class="save-btn" id="saveBtn" type="submit">Save</button>
      </form>
    </div>
  </div>
</div>

<script>
  function toggleEdit(editing) {
    if (editing) {
      const fields = ['firstName', 'lastName', 'username', 'contact', 'address'];
      fields.forEach(field => {
        document.getElementById(field + 'Display').style.display = 'none';
        document.getElementById(field + 'Input').style.display = 'inline-block';
      });
      document.querySelector('.edit-btn').style.display = 'none';
      document.getElementById('saveBtn').style.display = 'inline-block';
    }
  }
</script>
  <script src="C:\xampp\htdocs\ComShop\js\bootstrap.bundle.min.js"> </script>
</body>
</html>

