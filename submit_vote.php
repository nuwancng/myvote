<?php
session_start();
require_once('db_connection.php');

// Redirect if the user is not logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$mysqli = db_connect();
$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if the user already has a vote
$stmt = $mysqli->prepare('SELECT * FROM votes WHERE user_id = ?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$vote_result = $stmt->get_result();

if ($vote_result->num_rows > 0) {
    // Update existing vote
    $stmt = $mysqli->prepare('UPDATE votes SET response = ? WHERE user_id = ?');
} else {
    // Insert new vote
    $stmt = $mysqli->prepare('INSERT INTO votes (user_id, response) VALUES (?, ?)');
}

$stmt->bind_param('si', $user['id'], $_POST['response']);
$stmt->execute();

$_SESSION['message'] = 'Your vote has been successfully submitted/changed!';
header('Location: vote.php');
exit();
?>
