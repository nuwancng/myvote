<?php
function db_connect() {
    $host = 'localhost';
    $user = 'cpeusalcdl_myvote';
    $pass = '8OIQ1d,TccJw';
    $db = 'cpeusalcdl_myvote';

    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die('Database connection failed: ' . $mysqli->connect_error);
    }

    return $mysqli;
}
?>
