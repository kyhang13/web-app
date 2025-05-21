<?php
session_start();
require 'includes/db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header('Location: signup.php');
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username already exists in the 'users' table
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        $_SESSION['error'] = "Username is already taken!";
        header('Location: signup.php');
        exit();
    }

    // Insert data into 'users' table
    $insertUser = $pdo->prepare("INSERT INTO users (firstname, lastname, username, email, password) VALUES (?, ?, ?, ?, ?)");
    $insertUser->execute([$firstname, $lastname, $username, $email, $hashed_password]);

    // Insert data into 'accounts' table (assuming default status is 'Active')
    $fullname = $firstname . ' ' . $lastname; // Combine first and last name
    $insertAccount = $pdo->prepare("INSERT INTO accounts (fullname, email, username, status) VALUES (?, ?, ?, ?)");
    $insertAccount->execute([$fullname, $email, $username, 'Active']);

    // Set success message and redirect to login page
    $_SESSION['success'] = "Registration successful! Please log in.";
    header('Location: login.php');
    exit();
}
?>
