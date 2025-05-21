<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require 'includes/db.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['g-recaptcha-response'])) {
        $_SESSION['error'] = "Please complete the reCAPTCHA.";
        header('Location: login.php');
        exit();
    }

    $recaptchaSecret = $_ENV['RECAPTCHA_SECRET_KEY'];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}"
    );
    $captchaSuccess = json_decode($verify);

    if (empty($captchaSuccess) || !$captchaSuccess->success) {
        $_SESSION['error'] = "Captcha Verification failed. Please try again.";
        header('Location: login.php');
        exit();
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Assuming password in db is hashed
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role_as'] = $user['role_as'];

            if ($user['role_as'] == 1) {
                $_SESSION['message'] = "Welcome Admin!";
                header('Location: Admin/index.php');
            } else {
                $_SESSION['message'] = "Login successful!";
                header('Location: customer/products.php');
            }
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password.";
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header('Location: login.php');
        exit();
    }
}
?>
