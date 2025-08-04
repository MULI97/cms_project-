<?php
session_start();
session_unset();     // Clear all session variables
session_destroy();   // Destroy the session

// OPTIONAL: Clear session cookie for security
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to the sign-in page (adjust path as needed)
header("Location: ../index.php");
exit();
?>
