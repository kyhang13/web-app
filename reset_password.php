<?php

session_start();

require 'includes/db.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['reset_code_verified']) || !$_SESSION['reset_code_verified']) {
    header('Location: enter_code.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("UPDATE users SET password =? WHERE email =?");
        $stmt->execute([$hashedPassword, $_SESSION['reset_email']]);

        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_code_verified']);

        $_SESSION['success'] = 'Your password has been reset successfully.';
        header('Location: login.php');
        exit();

    } else {
        $_SESSION['error'] = 'Password do not match. Please try again.';
    }

}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Singup</title>
        <link rel="stylesheet"href="Style/style.css">
        <meta charset="utf-8">
    </head>
    <body>
        <div class="container">
            <div class="card">
                <img src="images/wp.png" alt="This is Logo">
                <h4 class="title">Reset Password</h4>

                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['success'] . '</div>';
                    unset($_SESSION['success']);
                }
                ?>

                <form action="reset_password.php" method="POST">
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Enter New Password" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Reset</button>
                </form>
            </div>
        </div>
    </body>

</html>