<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'connection.php';

// Redirect if not logged in or not an Author
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Author') {
    header("Location: index.php");
    exit();
}

// Fetch latest 6 articles
$sql = "SELECT a.article_title, a.article_body, a.article_created_date, u.username 
        FROM articles a
        JOIN users u ON a.article_author_id = u.user_id
        ORDER BY a.article_created_date DESC
        LIMIT 6";
$stmt = $conn->prepare($sql);
$stmt->execute();
$articles = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Articles - Author</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Latest Articles</h2>
    <a href="dashboard_author.php">← Back to Dashboard</a>
    <br><br>

    <?php if ($articles->num_rows > 0): ?>
        <?php while ($row = $articles->fetch_assoc()): ?>
            <div class="article-card">
                <h3><?php echo htmlspecialchars($row['article_title']); ?></h3>
                <p><strong>By:</strong> <?php echo htmlspecialchars($row['username']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($row['article_created_date']); ?></p>
                <p><?php echo nl2br(htmlspecialchars(substr($row['article_body'], 0, 200))) . '...'; ?></p>
            </div>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No articles found.</p>
    <?php endif; ?>
</body>
</html>
