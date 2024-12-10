<?php
session_start();
require_once('db_connection.php');

// Redirect if the user is not logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
    $response = trim($_POST['response']);

    $mysqli = db_connect();

    // Fetch user details
    $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $user_id = $user['id'];

        // Insert or update vote
        $stmt = $mysqli->prepare('
            INSERT INTO votes (user_id, response) 
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE response = ?
        ');
        $stmt->bind_param('iss', $user_id, $response, $response);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Your vote has been successfully submitted/changed!';
        } else {
            $_SESSION['message'] = 'An error occurred while submitting your vote.';
        }
    } else {
        $_SESSION['message'] = 'Unable to find your user details.';
    }

    header('Location: vote.php');
    exit();
} else {
    $_SESSION['message'] = 'Invalid request.';
    header('Location: vote.php');
    exit();
}
