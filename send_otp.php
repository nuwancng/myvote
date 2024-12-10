<?php
session_start();
require_once('db_connection.php'); // Database connection file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include Composer's autoloader
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $_SESSION['message'] = 'Invalid email format.';
        header('Location: index.php');
        exit();
    }

    $mysqli = db_connect();

    // Check if email exists
    $stmt = $mysqli->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['message'] = 'OTP sent to your email.';
    } else {
        $_SESSION['message'] = 'New user detected. OTP sent.';
    }

    // Generate OTP
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;

    // Configure PHPMailer to send emails via SMTP
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'mail.spacemail.com'; // Use your SMTP provider
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@myvote.click'; // Replace with your email
        $mail->Password   = '5fFDD108-d847-4f15-Bf4a-c6B89e88C53D'; // Replace with your app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('info@myvote.click', 'MyVote');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body    = "<p>Your OTP for login is: <b>$otp</b></p>";

        // Send the email
        if ($mail->send()) {
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['message'] = 'Failed to send OTP. Please try again.';
            header('Location: index.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['message'] = 'Failed to send email: ' . $mail->ErrorInfo;
        header('Location: index.php');
        exit();
    }
}
?>
