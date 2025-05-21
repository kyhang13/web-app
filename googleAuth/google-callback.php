<?php
require_once '../vendor/autoload.php';
require_once '../includes/db.php'; // Kailangan nato para maka query sa database

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Create Google Client
$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT']);
$client->addScope('email');
$client->addScope('profile');

// Check if 'code' exists
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        $email = $userInfo->email;
        $name = $userInfo->name;
        $picture = $userInfo->picture;

        // Check kung naa ba ang user sa database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // User found, set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_as'] = $user['role_as'];
            $_SESSION['user_type'] = 'google';
            $_SESSION['user_email'] = $email;
            $_SESSION['user_image'] = $picture;

            // Redirect based on role
            if ($user['role_as'] == 1) {
                $_SESSION['success'] = 'Welcome Admin!';
                header('Location: ../Admin/index.php');
            } else {
                $_SESSION['success'] = 'Login Successful!';
                header('Location: ../customer/products.php');
            }
            exit();
        } else {
            // No user found
            $_SESSION['error'] = 'No user found. Please sign up first.';
            header('Location: ../login.php');
            exit();
        }

    } else {
        $_SESSION['error'] = 'Login with Google failed!';
        header('Location: ../login.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'Login with Google failed!';
    header('Location: ../login.php');
    exit();
}
?>
