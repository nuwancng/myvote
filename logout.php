<?php
// Start session
session_start();


$_SESSION = array();


// Destroy the session
session_unset();
session_destroy();

// Redirect the user back to index.php
header('Location: index.php');
exit();
?>
