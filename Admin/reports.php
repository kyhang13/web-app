<?php
session_start();
require '../includes/db.php';

// Check admin access
if (!isset($_SESSION['role_as']) || $_SESSION['role_as'] != 1) {
    $_SESSION['error'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit();
}

// Fetch monthly sales data (bookings + order_completed)
try {
    $stmt = $pdo->query("
        SELECT 
            MONTH(booked_date) AS month,
            YEAR(booked_date) AS year,
            SUM(total_price) AS total_sales
        FROM bookings
        WHERE status != 'Cancelled'
        GROUP BY YEAR(booked_date), MONTH(booked_date)

        UNION ALL

        SELECT 
            MONTH(delivery_date) AS month,
            YEAR(delivery_date) AS year,
            SUM(total_price) AS total_sales
        FROM order_completed
        GROUP BY YEAR(delivery_date), MONTH(delivery_date)
    ");
    $rawSales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combine same month/year values
    $monthlySales = [];
    foreach ($rawSales as $row) {
        $key = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
        if (!isset($monthlySales[$key])) {
            $monthlySales[$key] = $row['total_sales'];
        } else {
            $monthlySales[$key] += $row['total_sales'];
        }
    }

    ksort($monthlySales);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <link rel="stylesheet" href="styles/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/ice.png">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <div class="heads"> R E P O R T S <img src="../images/wp.png" alt="logo"></div>
        <div class="sidebar2">
            <ul class="menu">
                <li><a href="index.php"><img src="../images/dashboard.png"><span style="padding-left: 10px;">Dashboard</span></a></li>
                <li><a href="products.php"><img src="../images/product.png"><span style="padding-left: 10px;">Products</span></a></li>
                <li><a href="booking_table.php"><img src="../images/book.jpg"><span style="padding-left: 10px;">Bookings</span></a></li>
                <li><a href="reports.php" class="active"><img src="../images/report.png"><span style="padding-left: 10px;">Reports</span></a></li>
                <li><a href="account.php"><img src="../images/account.png"><span style="padding-left: 10px;">Accounts</span></a></li>
                <li><a href="settings.php"><img src="../images/settings.png"><span style="padding-left: 10px;">Settings</span></a></li>
                <li><a href="#" onclick="confirmLogout()"><img src="../images/logout.png"><span style="padding-left: 10px;">Logout</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="search"><input type="text" placeholder="Search..."></div>
            <div class="user-profile"><span>MCG Creamline</span><img src="../images/ice.png" alt="User"></div>
        </div>

        <div class="report-container">
            <div class="container mt-4">
                <h4 class="text-center">Monthly Sales Report</h4>
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Monthly Sales (₱)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($monthlySales)): ?>
                            <?php foreach ($monthlySales as $key => $total): 
                                [$year, $month] = explode('-', $key); ?>
                                <tr>
                                    <td><?= date("F", mktime(0, 0, 0, $month, 10)); ?></td>
                                    <td><?= $year; ?></td>
                                    <td>₱<?= number_format($total, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center">No sales data found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "../home.php";
        }
    }

    // Theme toggle
    window.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.body.classList.add(`${savedTheme}-mode`);
    });
</script>
</body>
</html>
