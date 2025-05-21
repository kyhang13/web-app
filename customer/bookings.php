<?php
session_start();
require '../includes/db.php';

if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $booked_date = $_POST['booked-date'];
    $product_name = $_POST['product-name'];
    $quantity = (int)$_POST['quantity'];
    $payment = isset($_POST['payment']) ? $_POST['payment'] : '';
    $total_price = (float)$_POST['total_price']; // Get total from form

    // Step 1: Check current stock
    $stockStmt = $pdo->prepare("SELECT stock FROM products WHERE product_name = ?");
    $stockStmt->execute([$product_name]);
    $product = $stockStmt->fetch();

    if ($product && $product['stock'] >= $quantity) {
        $pdo->beginTransaction();

        try {
            // Insert booking with total price
            $stmt = $pdo->prepare("INSERT INTO bookings (name, email, address, booked_date, product_name, quantity, payment_method, total_price, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $fullname,
                $email,
                $address,
                $booked_date,
                $product_name,
                $quantity,
                $payment,
                $total_price,
                'Pending'
            ]);

            // Update stock
            $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_name = ?");
            $updateStock->execute([$quantity, $product_name]);

            $pdo->commit();
            echo "<script>alert('Booking successful!'); window.location.href='products.php';</script>";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<script>alert('Booking failed. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Insufficient stock for the selected product.');</script>";
    }
}

// Fetch products for dropdown
$productOptions = [];
$fetch = $pdo->query("SELECT product_name, price FROM products WHERE stock > 0");
while ($row = $fetch->fetch(PDO::FETCH_ASSOC)) {
    $productOptions[$row['product_name']] = $row['price'];
}
?>

<!DOCTYPE html>
<html>
    <head>
            <title>Bookings</title>
            <link rel="stylesheet" href="styles/style.css">
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="icon" type="image/png" href="../images/ice.png">
            <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel ="stylesheet">

            <script>
                // Load theme on page load
                window.addEventListener('DOMContentLoaded', () => {
                    const savedTheme = localStorage.getItem('theme') || 'light';
                    document.body.classList.add(`${savedTheme}-mode`);
                });
            </script>

    </head>
    <style>
  /* ========== RESET & BASE ========== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background-color: #ffffff;
    font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
}

/* ========== LAYOUT ========== */
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

/* ========== TOPBAR ========== */
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

/* ========== MAIN CONTENT ========== */
.main-content {
    display: flex;
    flex-direction: column;
    flex: 1;
    background-color: #ffffff;
}

.card {
    width: 80%;
    min-height: 150px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.card-body {
    padding: 15px;
}

.card-img-top {
    width: 50px;
    height: 50px;
    object-fit: contain;
}

.card-img-top.cover {
    object-fit: cover;
}

.card-title {
    font-size: 14px;
    margin: 5px 0;
}

.card h3 {
    font-size: 20px;
    margin-top: 10px;
}

/* ========== INVENTORY & TABLE ========== */
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

table td .button {
    padding: 5px 10px;
    background-color: #f44336;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

table td .button2 {
    padding: 5px 10px;
    background-color: #fff240;
    color: black;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.button:hover {
    background-color: #d32f2f;
}

.add-product-form {
    margin: 20px 0;
    display: flex;
    justify-content: right;
    gap: 10px;
}

.add-product-form input,
.add-product-form button {
    padding: 10px;
    font-size: 16px;
    border-radius: 4px;
    width: 50%;
    border: 1px solid #ddd;
}

.add-product-form button {
    background-color: #4CAF50;
    color: white;
}

.add-product-form button:hover {
    background-color: #45a049;
}

/* ========== SETTINGS ========== */
.settings-container {
    background-color: #ffffff;
    border-radius: 20px;
    padding: 20px;
    margin: 20px;
    max-width: 1000px;
    font-size: 18px;
}

.settings-title {
    font-size: 40px;
    font-weight: bold;
    margin-bottom: 25px;
    text-align: center;
}

.section-title {
    margin-top: 30px;
    font-size: 20px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 300;
}

.form-select {
    width: 200px;
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 8px;
}

.save-btn {
    display: block;
    margin: 40px auto 0;
    width: 200px;
    text-align: center;
}

.content {
    padding-top: 3px;
    margin-top: 3px;
    overflow-y: auto;
}

.table-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}

.status-active {
    color: green;
    font-weight: bold;
}

.status-inactive {
    color: red;
    font-weight: bold;
}

.btn-action {
    margin-right: 5px;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
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
    background-color: #2a2a2a;  /* Darker background for cards */
    color: #e0e0e0;  /* Lighter text for cards */
}

body.dark-mode .card-title {
    color: #f0f0f0;  /* Lighter card titles */
}

body.dark-mode .card-body {
    background-color: #2a2a2a;  /* Consistent dark background for card body */
}

body.dark-mode .card-img-top {
    filter: brightness(0.7);  /* Darken the image slightly for better contrast */
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
                <div class="heads"> B O O K I N G S
                <img src="../images/wp.png" alt="logo"> 
            </div>
          
            <ul class="menu">
                <li>
                    <a href="products.php">
                        <img src="../images/product.png" alt="products">
                        <span style="padding-left: 10px;">Products</span>
                    </a>
                </li>
                <li>
                    <a href="bookings.php" class="active">
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
                        // Display a confirmation dialog
                        const userConfirmed = confirm("Are you sure you want to log out?");
                        
                        // If the user clicks "OK", redirect to the home page (logout)
                        if (userConfirmed) {
                            window.location.href = "../home.php";
                        }
                    }
                </script>
            </ul>  </div>
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

    <style>
.booking-form {
    width: 100vh;
    max-width: 700px;
    margin: 60px auto;
    background:rgb(242, 241, 238); /* paper-like off-white */
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    border: 1px solid #e0ddd4;

    background-image: linear-gradient(0deg, transparent 94%, rgba(0,0,0,0.02) 96%, transparent 100%), 
                      linear-gradient(90deg, transparent 94%, rgba(0,0,0,0.02) 96%, transparent 100%);
    background-size: 100% 40px, 40px 100%; /* gives it a subtle grid/texture like notebook or craft paper */
}

        .form-group {
            margin-bottom: 20px;
        }
    </style>
<div class="content">
<div class="booking-form">
    <h2 class="text-center">Booking Form</h2>
    <form method="POST">
        <div class="form-group">
            <label for="fullname">Fullname</label>
            <input type="text" class="form-control" name="fullname" id="fullname" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" name="address" id="address" required>
        </div>

        <div class="form-group">
            <label for="booked-date">Booked Date</label>
            <input type="date" class="form-control" name="booked-date" id="booked-date" required>
        </div>

        <div class="form-group">
            <label for="product-name">Product</label>
            <select class="form-control" name="product-name" id="product-name" required>
                <option value="">Select Product</option>
                <?php foreach ($productOptions as $name => $price): ?>
                    <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" name="quantity" id="quantity" min="1" required>
        </div>

        <div class="form-group">
            <label for="total">Total Price</label>
            <input type="text" class="form-control" id="total" readonly>
            <!-- Hidden input to send total price -->
            <input type="hidden" name="total_price" id="total_price">
        </div>

        <div class="form-group">
            <label style="font-size: larger;">Payment Method</label>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="payment" value="cod" id="cod">
                <label for="cod" class="form-check-label">Cash on Delivery</label>
            </div>
        </div>

        <button type="submit" class="btn btn-outline-dark" name="submit">Book Now</button>
    </form>
    <a href="products.php" class="btn btn-secondary mb-3">&larr; Back</a>
    </div>
</div>

<script>
    const productPrices = <?= json_encode($productOptions) ?>;
    const productSelect = document.getElementById('product-name');
    const quantityInput = document.getElementById('quantity');
    const totalDisplay = document.getElementById('total');
    const totalHidden = document.getElementById('total_price');

    function updateTotal() {
        const product = productSelect.value;
        const quantity = parseInt(quantityInput.value) || 0;

        if (product in productPrices && quantity > 0) {
            const total = productPrices[product] * quantity;
            totalDisplay.value = '$' + total.toFixed(2);
            totalHidden.value = total.toFixed(2);
        } else {
            totalDisplay.value = '';
            totalHidden.value = '';
        }
    }

    productSelect.addEventListener('change', updateTotal);
    quantityInput.addEventListener('input', updateTotal);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>