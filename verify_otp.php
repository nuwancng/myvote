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
            $stmt = $mysqli->prepare('INSERT INTO users (email, first_name, last_name, last_logged_in) VALUES (?, "", "", NOW())');
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
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
</head>
<body>
    <h1>Verify Your OTP</h1>

    <?php
    if (isset($_SESSION['message'])) {
        echo "<p style='color: red'>{$_SESSION['message']}</p>";
        unset($_SESSION['message']);
    }
    ?>

    <form method="POST">
        <label for="otp">Enter OTP:</label>
        <input type="text" name="otp" id="otp" required>
        <button type="submit">Verify OTP</button>
    </form>
</body>
</html>
