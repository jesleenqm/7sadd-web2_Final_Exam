<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$db = 'comshop';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
$message = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}


$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rate = $_POST['rate'];
    $time = $_POST['time'];
    $stmt = $conn->prepare("INSERT INTO hourly_rates (rate, time, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ds", $rate, $time);
    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='alert alert-success'>Hourly rate updated successfully!</div>";
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>Error updating hourly rate.</div>";
    }
    $stmt->close();
    header("Location: hourly_rate.php");
    exit();
}


$result = $conn->query("SELECT * FROM hourly_rates ORDER BY id DESC LIMIT 2");
$rates = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hourly Rate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f0f0f0; }
        .container { display: flex; height: 100vh; }
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
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin: 10px 0; text-align: center; }
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
        .logout-btn:hover { background: darkred; }
        .main {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }
        .form-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .history {
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .history h4 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="dashboard.php">My Profile</a></li>
            <li><a href="hourly_rate.php">Hourly Rate</a></li>
            <li><a href="report.php">Reports</a></li>
            <li><a href="register.php">Register User</a></li>
        </ul>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="form-section">
            <h2>Update Hourly Rate</h2>
            <?= $message ?>
            <form method="POST" class="rate-form mt-4">
                <div class="mb-3">
                    <label for="rate" class="form-label">Hourly Rate (₱)</label>
                    <input type="number" step="0.01" class="form-control" name="rate" id="rate" required>
                </div>
                <div class="mb-3">
                    <label for="time" class="form-label">Time (in minutes)</label>
                    <input type="number" step="1" class="form-control" name="time" id="time" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Rate</button>
            </form>
        </div>

        <?php if (!empty($rates)): ?>
            <div class="history mt-4">
                <h4>Rate History</h4>
                <ul class="list-group">
                    <?php foreach ($rates as $index => $rate): ?>
                        <li class="list-group-item">
                            <?= $index === 0 ? "<strong>Updated Rate</strong>" : "<strong>Previous Rate</strong>" ?>:
                            ₱<?= $rate['rate'] ?> for <?= $rate['time'] ?> minutes 
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
