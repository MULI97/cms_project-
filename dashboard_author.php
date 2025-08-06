<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Author') {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Author Dashboard</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, Author <?php echo htmlspecialchars($username); ?></h1>
        <div class="button-container">
            <a href="author_update_profile.php" class="button">Update My Profile</a>
            <a href="author_manage_articles.php" class="button">Manage My Articles</a>
            <a href="author_view_articles.php" class="button">View Articles</a>
            <a href="logout.php" class="button">Logout</a>
        </div>
    </div>
</body>
</html>
