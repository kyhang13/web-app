<?php

session_start();

require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredCode = $_POST['code'];

    $email = $_SESSION['email'];

    if (!isset($_SESSION['email'])) {
        $_SESSION['error'] = "No email session found; Please try again";
        header('Location: forgot-password.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT reset_code FROM users WHERE email =?");
    $stmt->execute([$email]);
    $user= $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($enteredCode === $user['reset_code']) {
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code_verified'] = true;

            header('Location: reset_password.php');
            exit();
        }else {
            $_SESSION['error'] = "Invalid Code. Please try again";
        }
    } else {
        $_SESSION['error'] = "No user found with that email";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>forgot-password</title>
        <link rel="stylesheet"href="Style/style.css">
        <meta charset="utf-8">
    </head>
    <body>
        <div class="container">
            <div class="card">
                <img src="images/wp.png" alt="This is Logo">
                <h4 class="title">Enter Code</h4>

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
                <form action="enter_code.php" method="POST">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="code" name="code" placeholder="Enter Code" required>
                    </div>
                    <button type="submit" class="btn-btn-primary w-100">Verify Code</button>
                </form>
            </div>
        </div>
    </body>

</html>