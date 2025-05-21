<?php
session_start(); // Start session to manage errors or success messages
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <img src="images/wp.png" alt="Logo">
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <form action="signup_validate.php" method="POST">
                <input class="form-control" type="text" placeholder="Firstname" name="firstname" required>
                <input class="form-control" type="text" placeholder="Lastname" name="lastname" required>
                <input class="form-control" type="text" placeholder="Username" name="username" required>
                <input class="form-control" type="email" placeholder="Email" name="email" required>
                <input class="form-control" type="password" placeholder="Password" name="password" required>
                <input class="form-control" type="password" placeholder="Confirm Password" name="confirm_password" required>
                
                <button type="submit">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
