<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['role_as']) || $_SESSION['role_as'] != 1) {
    $_SESSION['error'] = "Access denied. Admins only.";
    header("Location: ../login.php");
    exit();
}

// Monthly Sales for Chart
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
    $chartLabels = array_map(function ($key) {
        return date("F Y", strtotime($key . '-01'));
    }, array_keys($monthlySales));
    $chartData = array_values($monthlySales);

} catch (PDOException $e) {
    die("Error fetching chart data: " . $e->getMessage());
}

// User Count
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM accounts");
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
} catch (PDOException $e) {
    $userCount = "Error";
}

// Total Orders
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total_orders FROM bookings");
    $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];
} catch (PDOException $e) {
    $totalOrders = "Error";
}

// Total Sales
try {
    $stmt = $pdo->query("SELECT SUM(total_price) AS total FROM bookings WHERE status != 'Cancelled'");
    $bookingSales = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT SUM(total_price) AS total FROM order_completed");
    $completedSales = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $totalSales = number_format($bookingSales + $completedSales, 2);
} catch (PDOException $e) {
    $totalSales = "Error";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../images/ice.png">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
.content {
    height: calc(100vh - 70px);
    overflow-y: auto;
    padding: 20px;
    position: relative;
}
</style>
<body>
<div class="dashboard">
    <div class="sidebar">
        <div class="heads">D A S H B O A R D <img src="../images/wp.png" alt="logo"></div>
        <div class="sidebar2">
            <ul class="menu">
                <li><a href="index.php" class="active"><img src="../images/dashboard.png" alt="dashboard"><span style="padding-left: 10px;">Dashboard</span></a></li>
                <li><a href="products.php"><img src="../images/product.png" alt="products"><span style="padding-left: 10px;">Products</span></a></li>
                <li><a href="booking_table.php"><img src="../images/book.jpg" alt="bookings"><span style="padding-left: 10px;">Bookings</span></a></li>
                <li><a href="reports.php"><img src="../images/report.png" alt="reports"><span style="padding-left: 10px;">Reports</span></a></li>
                <li><a href="account.php"><img src="../images/account.png" alt="accounts"><span style="padding-left: 10px;">Accounts</span></a></li>
                <li><a href="settings.php"><img src="../images/settings.png" alt="settings"><span style="padding-left: 10px;">Settings</span></a></li>
                <li><a href="#" onclick="confirmLogout()"><img src="../images/logout.png"><span style="padding-left: 10px;">Logout</span></a></li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="search"><input type="text" placeholder="Search..."></div>
            <div class="user-profile">
                <span>MCG Creamline</span>
                <img src="../images/ice.png" alt="User">
            </div>
        </div>

        <div class="content">
            <div class="row justify-content-center text-center">
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <img src="../images/sales.png" class="card-img-top" alt="sales">
                            <h5 class="card-title">Total Sales</h5>
                            <h5 class="card-title">-------------</h5>
                            <h3>₱<?= $totalSales ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <img src="../images/products.png" class="card-img-top" alt="Orders">
                            <h5 class="card-title">Orders</h5>
                            <h5 class="card-title">-------------</h5>
                            <h3><?= $totalOrders ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <img src="../images/human.png" class="card-img-top" alt="users">
                            <h5 class="card-title">Users</h5>
                            <h5 class="card-title">-------------</h5>
                            <h3><?= $userCount ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-4">
                <h4 class="text-center">Sales Report</h4>
                <div class="container mt-5">
                    <h5 class="text-center">Monthly Sales Chart</h5>
                    <canvas id="salaryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Script -->
<script>
    const chartLabels = <?= json_encode($chartLabels) ?>;
    const chartData = <?= json_encode($chartData) ?>;

    const ctx = document.getElementById('salaryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Monthly Sales (₱)',
                data: chartData,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function toggleMode() {
        if (document.body.classList.contains("light-mode")) {
            document.body.classList.replace("light-mode", "dark-mode");
            localStorage.setItem("theme", "dark");
        } else {
            document.body.classList.replace("dark-mode", "light-mode");
            localStorage.setItem("theme", "light");
        }
    }

    window.onload = () => {
        const savedTheme = localStorage.getItem("theme") || "light";
        document.body.classList.add(`${savedTheme}-mode`);
    };

    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "../home.php";
        }
    }
</script>
</body>
</html>
