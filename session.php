<?php
// Set session cookie parameters
$params = session_get_cookie_params();
$params["lifetime"] = 3600; // set session cookie expiration time to 1 hour
$params["httponly"] = true;
$params["secure"] = true;
session_set_cookie_params($params);

// Regenerate session ID if it hasn't been regenerated yet
if (!isset($_SESSION['regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['regenerated'] = true;
}

// Start session
session_start();
?>
