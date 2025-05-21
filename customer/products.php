<?php
session_start();
require '../includes/db.php';

// Allow only customers (role_as = 0)
if (!isset($_SESSION['role_as']) || $_SESSION['role_as'] != 0) {
    $_SESSION['error'] = "Access denied. Customers only.";
    header("Location: ../login.php");
    exit();
}

// Fetch products from the database
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="styles/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/ice.png">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script>
                // Load theme on page load
                window.addEventListener('DOMContentLoaded', () => {
                    const savedTheme = localStorage.getItem('theme') || 'light';
                    document.body.classList.add(`${savedTheme}-mode`);
                });
            </script>

</head>
<style>
    /* ========== Reset & Base ========== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        background-color: #ffffff;
        font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
    }

    /* ========== Layout ========== */
    .dashboard {
        display: flex;
        height: 100vh;
    }

    .sidebar {
        width: 20%;
        padding: 15px;
        background: linear-gradient(rgba(255, 245, 247, 0.9), rgba(236, 112, 138, 0.6));
        color: #3c3c3c;
    }

    .sidebar .heads {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        font-size: 18px;
    }

    .sidebar .heads img {
        width: 250px;
        height: 150px;
        margin-bottom: 20px;
        padding: 5px;
        background-color: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .sidebar .menu {
        list-style: none;
    }

    .sidebar .menu li {
        margin-bottom: 10px;
    }

    .sidebar .menu li a {
        color: black;
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 4px;
    }

    .sidebar .menu li a img {
        width: 35px;
        height: 35px;
        padding: 4px;
        background-color: rgb(231, 167, 177);
        border-radius: 10px;
    }

    .sidebar .menu li a:hover {
        background-color: rgba(250, 222, 227, 0.4);
    }

    .sidebar .menu li a.active {
        background-color: rgb(255, 161, 177);
    }

    /* ========== Topbar ========== */
    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background-color: #f8c1c8;
        font-family: 'Verdana', Geneva, Tahoma, sans-serif;
        font-size: 16px;
        color: #333;
        border-bottom: 2px solid #e0aeb4;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        transition: background-color 0.3s ease-in-out;
    }

    .topbar:hover {
        background-color: #f29fa8;
    }

    .topbar .search input {
        padding: 10px;
        border: 1px solid #cccccc;
        border-radius: 20px;
        background-color: #fff;
        font-size: 14px;
        width: 250px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transition: width 0.3s ease-in-out;
    }

    .topbar .search input:focus {
        width: 300px;
        outline: none;
        border-color: #d7858c;
    }

    .topbar .user-profile {
        display: flex;
        align-items: center;
    }

    .topbar .user-profile img {
        margin-left: 10px;
        border-radius: 25px;
        width: 50px;
        height: 50px;
    }

    /* ========== Main Content ========== */
    .main-content {
        display: flex;
        flex-direction: column;
        flex: 1;
        background-color: #ffffff;
    }

    /* ========== Inventory & Table ========== */
    .inventory-container,
    .report-container {
        background-color: white;
        padding: 20px;
        margin: 20px;
        border-radius: 10px;
        height: 100vh;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        overflow-y: auto;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .total-products {
        text-align: center;
        font-size: 18px;
        margin-bottom: 20px;
    }

    .card {
        width: 80%;
        min-height: 150px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 15px;
        background-color: rgb(237, 192, 192);
    }
    .content2{
        padding: 20px;
        overflow-y: auto;
    }

    .card-img-top {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .card h3 {
        font-size: 20px;
        margin-top: 10px;
    }

    .card-title {
        font-size: 14px;
        margin: 5px 0;
    }

    /* ========== Table ========== */
    table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        margin: 0 auto;
    }

    table th, table td {
        padding: 12px;
        border: 1px solid #ddd;
    }

    table th {
        background-color: #4CAF50;
        color: white;
    }

    table tr:hover {
        background-color: #ebdfdf;
    }

    .btn-outline-dark {
        background-color: #4CAF50;
        color: white;
    }

   /* ========== DARK MODE ========== */
body.dark-mode {
    background-color: #1f1f1f;  /* Darker background for a more immersive dark mode */
    color: #e0e0e0;  /* Lighter text for better readability */
}

/* Form Elements */
body.dark-mode input,
body.dark-mode select,
body.dark-mode button {
    background-color: #2a2a2a;  /* Dark background for inputs */
    color: #e0e0e0;  /* Lighter text */
    border: 1px solid #555;  /* Subtle border for input elements */
}

body.dark-mode input:focus,
body.dark-mode select:focus,
body.dark-mode button:focus {
    background-color: #444;  /* Slightly lighter background on focus */
    border-color: #007bff;  /* Highlight with blue border on focus */
}

/* Sidebar */
body.dark-mode .sidebar,
body.dark-mode .sidebar2 {
    background: #222;  /* Darker sidebar */
    color: #e0e0e0;  /* Lighter text in sidebar */
}

body.dark-mode .sidebar .menu li a,
body.dark-mode .sidebar2 .menu li a {
    color: #e0e0e0;  /* Light text for menu links */
}

body.dark-mode .sidebar .menu li a:hover,
body.dark-mode .sidebar2 .menu li a:hover {
    background-color: rgba(100, 100, 100, 0.5);  /* Softer hover effect */
}

body.dark-mode .sidebar .menu li a.active {
    background-color: rgba(50, 50, 50, 0.7);  /* Active link background */
}

/* Topbar */
body.dark-mode .topbar {
    background: #333;  /* Darker topbar background */
    color: #e0e0e0;  /* Lighter text for the topbar */
    border-bottom: 1px solid #444;  /* Subtle border separation */
}

/* Main Content */
body.dark-mode .main-content {
    background-color: #333;  /* Lighter dark background for main content */
    color: #e0e0e0;  /* Light text color for readability */
}


body.dark-mode h2,
body.dark-mode h3 {
    color: #f0f0f0;  /* Lighter heading text */
}

body.dark-mode .booking-form {
    background-color: #3d3c3c;  /* Dark background for booking form */
}

/* Cards */
body.dark-mode .card {
    background-color:rgb(111, 109, 109);  /* Darker background for cards */
    color: #e0e0e0;  /* Lighter text for cards */
}

body.dark-mode .card-title {
    color: #f0f0f0;  /* Lighter card titles */
}

body.dark-mode .card-body {
    background-color:rgb(89, 89, 89);  /* Consistent dark background for card body */
}

body.dark-mode .card-img-top {
    filter: brightness(0.9);  /* Darken the image slightly for better contrast */
}

/* Buttons */
body.dark-mode .btn-outline-dark {
    background-color: #007bff;  /* Bright button color */
    color: white;  /* White text for button */
    border: none;
}

body.dark-mode .btn-outline-dark:hover {
    background-color: #0056b3;  /* Button color on hover */
}

/* Table */
body.dark-mode table th {
    background-color: #444;  /* Darker background for table headers */
    color: #fff;  /* Lighter text for table headers */
}

body.dark-mode table tr:hover {
    background-color: rgba(255, 255, 255, 0.1);  /* Subtle hover effect on rows */
}

body.dark-mode table td {
    color: #d0d0d0;  /* Slightly lighter text in table cells */
}

/* Inventory Containers */
body.dark-mode .inventory-container,
body.dark-mode .report-container {
    background-color: #333;  /* Dark background for containers */
    color: #e0e0e0;  /* Lighter text */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);  /* Subtle box-shadow */
}

/* Miscellaneous */
body.dark-mode #username,
body.dark-mode #email {
    color: #4fc3f7;  /* Cyan text for username/email */
    background-color: #333;  /* Dark background for form fields */
}

body.dark-mode .sidebar .menu li a img {
    background-color: rgb(76, 141, 141);  /* Soft green background for sidebar icons */
}

body.dark-mode .search input {
    background-color: #444;  /* Darker input field */
    color: #e0e0e0;  /* Light text color */
}

</style>

<body>
    <div class="dashboard">
        <div class="sidebar">
            <div class="heads">
                P R O D U C T
                <img src="../images/wp.png" alt="logo">
            </div>
            <ul class="menu">
                <li>
                    <a href="products.php" class="active">
                        <img src="../images/product.png" alt="products">
                        <span style="padding-left: 10px;">Products</span>
                    </a>
                </li>
                <li>
                    <a href="bookings.php">
                        <img src="../images/book.jpg" alt="booking form">
                        <span style="padding-left: 10px;">Booking Form</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <img src="../images/settings.png" alt="settings">
                        <span style="padding-left: 10px;">Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="confirmLogout()">
                        <img src="../images/logout.png" alt="logout">
                        <span style="padding-left: 10px;">Logout</span>
                    </a>
                </li>
                <script>
                    function confirmLogout() {
                        const userConfirmed = confirm("Are you sure you want to log out?");
                        if (userConfirmed) {
                            window.location.href = "../home.php";
                        }
                    }
                </script>
            </ul>
        </div>
        <div class="main-content">
            <div class="topbar">
                <div class="search">
                    <input type="text" placeholder="Search...">
                </div>
                <div class="user-profile">
                    <span>MCG Creamline</span>
                    <img src="../images/ice.png" alt="User">
                </div>
            </div>

            <div class="content2">
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="Product Image">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                                    <h6>Category: <?= htmlspecialchars($product['category']) ?></h6>
                                    <h6>Stocks: <?= $product['stock'] ?></h6>
                                    <p class="card-text">
                                        <h3>$<?= number_format($product['price'], 2) ?></h3>
                                    </p>
                                    <a href="bookings.php" class="btn btn-outline-dark">Go to booking form</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
