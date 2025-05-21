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
    
    <body>

    <div class="dashboard">
        <div class="sidebar">
            <div class="heads">
                S E T T I N G S
                <img src="../images/wp.png" alt="Logo">
            </div>
            <div class="sidebar2">
            <ul class="menu">
                <li><a href="index.php"><img src="../images/dashboard.png" alt="dashboard"><span style="padding-left: 10px;">Dashboard</span></a></li>
<li><a href="products.php"><img src="../images/product.png" alt="products"><span style="padding-left: 10px;">Products</span></a></li>
<li><a href="booking_table.php"><img src="../images/book.jpg" alt="bookings"><span style="padding-left: 10px;">Bookings</span></a></li>
<li><a href="reports.php"><img src="../images/report.png" alt="reports"><span style="padding-left: 10px;">Reports</span></a></li>
<li><a href="account.php" ><img src="../images/account.png" alt="accounts"><span style="padding-left: 10px;">Accounts</span></a></li>
<li><a href="settings.php" class="active"><img src="../images/settings.png" alt="settings"><span style="padding-left: 10px;">Settings</span></a></li>
<li>
    <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="logout"><span style="padding-left: 10px;">Logout</span></a>
</li>
<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "../home.php";
        }
    }
</script>

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
            <div class="content" style="overflow-y: auto;">
                <div class="settings-container" style="overflow-y: auto;">
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

                    <div class="section-title">Theme</div>
                    <div class="form-group">
                        <label for="themeSelect" class="form-label">Choose Theme</label>
                        <select class="form-select" id="themeSelect">
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>

                                    <!-- Backup & Restore -->
                <div class="section-title">Backup & Restore</div>
                <div class="form-group">
                    <button class="btn btn-outline-secondary" onclick="alert('Backup Created!')">Download Backup</button>
                    <button class="btn btn-outline-danger" onclick="alert('Restore Completed!')">Restore Backup</button>
                </div>

                <!-- Logs & Analytics -->
                <div class="section-title">Logs & Analytics</div>
                <div class="form-group">
                    <a href="index.html" class="active"> <button class="btn btn-outline-info">View Logs</button></a>
                    
                </div>

                    <button class="btn btn-primary save-btn" onclick="saveSettings()">Save Settings</button>
                </div>
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
