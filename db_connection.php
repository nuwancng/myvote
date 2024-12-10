<?php
function db_connect() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'user_system';

    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die('Database connection failed: ' . $mysqli->connect_error);
    }

    return $mysqli;
}
?>
