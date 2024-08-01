<?php
session_start();
require 'config.php'; // Ensure this file contains your DB connection code

// Function to get the user's IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // Check for IP from shared internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Check for IP passed from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // Default IP address
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];     
    $ipAddress = getUserIP();

    // Validate credentials
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && ($password == $user['password'])) {
        $userId = $user['id'];

        // Update user info
        $updateStmt = $pdo->prepare("UPDATE users SET latitude = ?, longitude = ?, ip_address = ? WHERE id = ?");
        $updateStmt->execute([$latitude, $longitude, $ipAddress, $userId]);

        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="manifest" href="/manifest.json">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(error) {
                    console.log('ServiceWorker registration failed: ', error);
                });
            });
        }
    </script>
</head>
<body>
    <form method="post" action="">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="hidden" id="latitude" name="latitude" required><br>
        <input type="hidden" id="longitude" name="longitude" required><br>
        <input type="submit" value="Login">
    </form>
    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                }, function() {
                    alert("Geolocation service failed.");
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
        
        window.onload = getLocation; // Get location when the page loads
    </script>
</body>
</html>
