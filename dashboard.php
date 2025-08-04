<?php
// dashboard.php

session_start();

// Redirect if user is not logged in or not Super_User
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'Super_User') {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super_User Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h2>Welcome to the Super_User Dashboard</h2>
    <div class="welcome">
        Hello, <strong><?= htmlspecialchars($username) ?></strong>!
    </div>

    <!-- Secure logout via POST -->
    <form method="POST" action="logout.php">
        <button type="submit" class="btn btn-danger">Log Out</button>
    </form>
</div>

</body>
</html>
