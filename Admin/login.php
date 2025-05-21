<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$siteKey = $_ENV['RECAPTCHA_SITE_KEY'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="Style/style.css">
    <meta charset="utf-8">
</head>
<body>
    <div class="container">
        <div class="card">
            <img src="images/wp.png" alt="This is Logo">

            <!-- Show Error -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Show Success -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="login_validate.php" method="POST">
                <input required class="form-control" type="text" placeholder="Username" name="username">
                <input required class="form-control" type="password" placeholder="Password" name="password">

                <div class="mb-3 text-center">
                    <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($siteKey) ?>"></div>
                </div>

                <button type="submit" class="btn">Log In</button>

                <div class="text-center mt-3">
                     <a href="googleAuth/google-login.php" class="btn">Sign up with Google</a>
                </div>

                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
                <p>Forgot Password? <a href="forgot-password.php">Click here!</a></p>
            </form>
        </div>
    </div>

    <script src="https://www.google.com/recaptcha/api.js"></script>
</body>
</html>
