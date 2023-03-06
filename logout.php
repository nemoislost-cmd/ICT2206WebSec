<?php
session_start();
session_unset(); // Clear session data
session_destroy(); // Destroy the session
header("Location: login.php"); // Redirect to login page
exit();
?>