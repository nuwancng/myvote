<?php
session_start();
require_once('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_otp = $_POST['otp'];

    if (!isset($_SESSION['otp'])) {
        $_SESSION['message'] = "Session expired. Please try again.";
        header('Location: index.php');
        exit();
    }

    if ($input_otp == $_SESSION['otp']) {
        $mysqli = db_connect();

        $email = $_SESSION['email'];
        $stmt = $mysqli->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Insert new user if not found
            $stmt = $mysqli->prepare('INSERT INTO users (email, first_name, last_name) VALUES (?, "", "", NOW())');
            $stmt->bind_param('s', $email);
            $stmt->execute();
        } else {
            // Update last logged in timestamp
            $stmt = $mysqli->prepare('UPDATE users SET last_logged_in = NOW() WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
        }

        // Redirect user to dashboard
        $_SESSION['email'] = $email;
        header('Location: dashboard.php');
        exit();
    } else {
        $_SESSION['message'] = "Invalid OTP, please try again.";
        header('Location: index.php');
        exit();
    }
}
?>
