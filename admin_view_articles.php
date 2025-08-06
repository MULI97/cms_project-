<?php
session_start();
require_once 'connection.php';

// Restrict access to only logged-in administrators
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: index.php");
    exit();
}

// Fetch last 6 articles in descending order
$sql = "SELECT article_title, article_content, article_created_date FROM articles ORDER BY article_created_date DESC LIMIT 6";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - View Articles</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/topnav_admin.php'; ?>
    
    <div class="container">
        <h2>Last 6 Articles</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="article-list">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="article-card">
                        <h3><?= htmlspecialchars($row['article_title']) ?></h3>
                        <p><?= nl2br(htmlspecialchars(substr($row['article_content'], 0, 300))) ?>...</p>
                        <p><strong>Published on:</strong> <?= htmlspecialchars($row['article_created_date']) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No articles found.</p>
        <?php endif; ?>

        <br><a href="dashboard_admin.php">← Back to Dashboard</a>
    </div>
</body>
</html>
