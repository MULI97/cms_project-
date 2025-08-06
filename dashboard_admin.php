<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: index.php");
    exit();
}

// Fetch the admin's name from the session
$adminName = $_SESSION['full_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Administrator Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'topnav.php'; ?>

<div class="container">
    <h2>Welcome Administrator</h2>
    <p class="welcome">Hello, <?php echo htmlspecialchars($adminName); ?>!</p>

    <div class="dashboard-buttons">
        <a href="admin_update_profile.php" class="dashboard-btn">Update My Profile</a>
        <a href="admin_manage_authors.php" class="dashboard-btn">Manage Authors</a>
        <a href="admin_view_articles.php" class="dashboard-btn">View Articles</a>
        <a href="logout.php" class="dashboard-btn logout">Logout</a>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
