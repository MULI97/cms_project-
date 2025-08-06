<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

    <div class="dashboard-buttons">
        <form action="update_profile.php" method="get">
            <button type="submit" class="btn">Update My Profile</button>
        </form>

        <form action="manage_users.php" method="get">
            <button type="submit" class="btn">Manage Other Users</button>
        </form>

        <form action="view_articles.php" method="get">
            <button type="submit" class="btn">View Articles</button>
        </form>

        <form action="logout.php" method="post">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>
</div>

</body>
</html>
