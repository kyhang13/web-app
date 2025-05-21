<?php

require_once '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Create a new Google_Client instance
$client = new Google_Client();

// Set the Client ID, Client Secret, and Redirect URI from environment variables
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT']);

// Set the required scopes
$client->addScope('email');
$client->addScope('profile');

// Redirect the user to the Google authorization URL
header('Location: ' . $client->createAuthUrl());
exit();

?>
