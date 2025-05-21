<?php
session_start();
require 'includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Optional: Load .env if you're using environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $reset_code = rand(10000, 999999);

        $update = $pdo->prepare("UPDATE users SET reset_code = ? WHERE email = ?");
        $update->execute([$reset_code, $email]);

        $_SESSION['email'] = $email;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['USER_EMAIL'];   // Set in your .env or environment
            $mail->Password = $_ENV['USER_PASS'];    // App-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($_ENV['USER_EMAIL'], 'Ailyn Cabanas'); // ✅ Corrected method
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Verification Code';
            $mail->Body = "
                <div style='background-color: white; padding: 20px; font-family: Arial, sans-serif'>
                    <p>Hello,</p>
                    <p>Use the code below to reset your password:</p>
                    <h2>$reset_code</h2>
                </div>";
            $mail->AltBody = "Hello, use this code to reset your password: $reset_code";

            $mail->send();

            $_SESSION['Email_sent'] = true;
            $_SESSION['success'] = "A verification code has been sent to your email.";
            header('Location: enter_code.php');
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to send verification email. Mailer Error: {$mail->ErrorInfo}";
            header('Location: forgot-password.php');
            exit();
        }

    } else {
        $_SESSION['error'] = "No user found with that email.";
        header('Location: forgot-password.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="Style/style.css">
    <meta charset="utf-8">
</head>
<body>
    <div class="container">
        <div class="card">
            <img src="images/wp.png" alt="This is Logo">
            <h4>Enter your email to continue</h4>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['error']; unset($_SESSION['error']) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <?= $_SESSION['success']; unset($_SESSION['success']) ?>
                </div>
            <?php endif; ?>

            <form action="forgot-password.php" method="POST">
                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Enter Email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Code</button> <!-- ✅ Fixed button class -->
            </form>
        </div>
    </div>
</body>
</html>
