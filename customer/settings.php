<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the data from session
$username = $_SESSION['username'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>

    <link rel="icon" type="image/png" href="../images/ice.png">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    background-color: #333;
}

body.light-mode input,
body.light-mode select,
body.light-mode button {
    background-color: #fff;
    color: #000;
    border: 1px solid #ccc;
}

body.dark-mode .sidebar,
body.dark-mode .sidebar2 {
    background: #302f2f !important;
    color: #ffffff;
    background-image: none !important;
}

body.dark-mode .sidebar .menu li a,
body.dark-mode .sidebar2 .menu li a {
    color: #ffffff;
}

body.dark-mode .sidebar .menu li a:hover,
body.dark-mode .sidebar2 .menu li a:hover {
    background-color: rgba(151, 149, 149, 0.1);
}

body.dark-mode .sidebar .menu li a.active {
    background-color: rgba(138, 138, 138, 0.5);
}

body.dark-mode .topbar {
    background: #3d3c3c !important;
    color: #ffffff;
    border-bottom: #3d3c3c;
}

body.dark-mode .content,
body.dark-mode .settings-container {
    background-color: #3d3c3c;
    color: #ffffff;
}

body.dark-mode .sidebar .menu li a img {
    background-color: rgb(76, 141, 141);
}

body.dark-mode .inventory-container {
    background-color:#3d3c3c;
}

body.dark-mode #username,
body.dark-mode #email {
    color: cyan;
    background-color: #3c3c3c;
}
body.dark-mode .main-content{
    background-color: #302f2f;
    color:#ffffff;
}
body.dark-mode h2{
    color: #ffffff;
}
body.dark-mode .report-container {
    background-color:#3d3c3c;
}
</style>
    
    <body>

    <div class="dashboard">
        <div class="sidebar">
            <div class="heads">
                S E T T I N G S
                <img src="../images/wp.png" alt="Logo">
            </div>
            <div class="sidebar2">
            <ul class="menu">
                <li><a href="products.php"><img src="../images/product.png"><span style="padding-left: 10px;">Products</span></a></li>
                <li>
                    <a href="bookings.php">
                        <img src="../images/book.jpg" alt="booking form">
                        <span style="padding-left: 10px;">Booking Form</span>
                    </a>
                </li>
                <li><a href="settings.php" class="active"><img src="../images/settings.png"><span style="padding-left: 10px;">Settings</span></a></li>
                <li><a href="#" onclick="confirmLogout()"><img src="../images/logout.png"><span style="padding-left: 10px;">Logout</span></a></li>
            </ul>
            </div>
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
            <div class="content" style="height: 100vh; overflow-y: auto; padding: 10px;">
                <div class="settings-title">Settings</div>

                <div class="section-title">Profile</div>
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($username) ?>" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($email) ?>" disabled>
                </div>

                    <div class="section-title">Notifications</div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="emailNotifs" checked>
                        <label class="form-check-label" for="emailNotifs">Email Notifications</label>
                    </div>

                    <div class="section-title">Theme</div>
                    <div class="form-group">
                        <label for="themeSelect" class="form-label">Choose Theme</label>
                        <select class="form-select" id="themeSelect">
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>

                    <button class="btn btn-primary save-btn" onclick="saveSettings()">Save Settings</button>
                </div>
            </div>
        </div>

    <script>
        // Load theme on page load
        window.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.classList.add(`${savedTheme}-mode`);
            document.getElementById('themeSelect').value = savedTheme;
        });



        function saveSettings() {
            const theme = document.getElementById('themeSelect').value;
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;

            document.body.classList.remove('light-mode', 'dark-mode');
            document.body.classList.add(`${theme}-mode`);
            localStorage.setItem('theme', theme);

            console.log({
                username,
                email,
                theme,
                profileVisibility: document.getElementById('profileVisibility').checked,
                emailNotifs: document.getElementById('emailNotifs').checked
            });
        }

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "../home.php";
            }
        }
    </script>
</body>
</html>
