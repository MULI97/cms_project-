<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'Super_User') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Super_User Dashboard</title></head>
<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>You have successfully signed in as Super_User.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
