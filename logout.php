<?php
session_start(); // Ensures access to the session

// Clear session array
$_SESSION = array();

// Destroy session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Destroys session

header("Location: login.php"); // Redirect to login page or another page as appropriate
exit;
