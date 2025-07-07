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

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Admin sees all non-admin sessions
if ($role === 'admin') {
    $sql = "SELECT sl.id, u.username, sl.login_time, sl.logout_time 
            FROM session_logs sl
            JOIN users u ON sl.user_id = u.id
            WHERE u.role != 'admin'
            AND DATE(sl.login_time) = '$date_filter'
            ORDER BY sl.login_time DESC";
} else {
    // User sees only their own sessions
    $sql = "SELECT sl.id, u.username, sl.login_time, sl.logout_time 
            FROM session_logs sl
            JOIN users u ON sl.user_id = u.id
            WHERE sl.user_id = $user_id
            AND DATE(sl.login_time) = '$date_filter'
            ORDER BY sl.login_time DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Session Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f0f0;
        }
        .container {
            background: white;
            padding: 30px;
            margin-top: 30px;
            border-radius: 10px;
        }
        .total-revenue {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Session Report</h2>

    <!-- Date Filter -->
    <form method="get" class="mb-3">
        <select name="date" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
            <option value="<?= date('Y-m-d'); ?>" <?= ($date_filter == date('Y-m-d')) ? 'selected' : ''; ?>>Today</option>
            <option value="<?= date('Y-m-d', strtotime('-1 day')); ?>" <?= ($date_filter == date('Y-m-d', strtotime('-1 day'))) ? 'selected' : ''; ?>>Yesterday</option>
        </select>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <?php if ($role === 'admin'): ?>
                <th>Username</th>
            <?php endif; ?>
            <th>Login Time</th>
            <th>Logout Time</th>
            <th>Total Time Consumed</th>
            <th>Hourly Rate (₱/mins)</th>
            <th><?= ($role === 'admin') ? 'Total Revenue' : 'Total Amount'; ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total_revenue_all = 0;
        while ($row = $result->fetch_assoc()):
            $login_time = new DateTime($row['login_time']);
            $logout_time = !empty($row['logout_time']) ? new DateTime($row['logout_time']) : null;

            $total_minutes = 0;
            $rate = 0;
            $time_unit = 0;
            $revenue = 0;

            if ($logout_time) {
               $seconds_diff = $logout_time->getTimestamp() - $login_time->getTimestamp();
                $total_minutes = ceil($seconds_diff / 60); // Round up to the next full minute


                $rate_result = $conn->query("SELECT rate, time FROM hourly_rates WHERE created_at <= '{$login_time->format('Y-m-d H:i:s')}' ORDER BY created_at DESC LIMIT 1");
                if ($rate_result && $rate_result->num_rows > 0) {
                    $rate_data = $rate_result->fetch_assoc();
                    $rate = $rate_data['rate'];
                    $time_unit = $rate_data['time'];

                    $per_minute_rate = $rate / $time_unit;
                    $revenue = $per_minute_rate * $total_minutes;
                    $total_revenue_all += $revenue;
                }
            }
        ?>
            <tr>
                <?php if ($role === 'admin'): ?>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                <?php endif; ?>
                <td><?= $login_time->format('Y-m-d H:i:s'); ?></td>
                <td><?= $logout_time ? $logout_time->format('Y-m-d H:i:s') : 'N/A'; ?></td>
                <td><?= $total_minutes; ?> min</td>
                <td>₱<?= number_format($rate, 2); ?> / <?= $time_unit ?> mins</td>
                <td>₱<?= number_format($revenue, 2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="total-revenue">
        <?= ($role === 'admin') ? 'Total Revenue' : 'Total Amount'; ?>: ₱<?= number_format($total_revenue_all, 2); ?>
    </div>

    <a href="<?= ($role === 'admin') ? 'dashboard.php' : 'user_dashboard.php'; ?>" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
