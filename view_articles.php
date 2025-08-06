<?php
// view_articles.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'connection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle search input
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $search_param = "%{$search}%";
    $stmt = $conn->prepare("
        SELECT a.id, a.title, a.summary, a.article_created_date, u.name AS author
        FROM articles a
        JOIN users u ON a.user_id = u.id
        WHERE a.title LIKE ?
        ORDER BY a.article_created_date DESC
        LIMIT 6
    ");
    $stmt->bind_param("s", $search_param);
} else {
    $stmt = $conn->prepare("
        SELECT a.id, a.title, a.summary, a.article_created_date, u.name AS author
        FROM articles a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.article_created_date DESC
        LIMIT 6
    ");
}

$stmt->execute();
$result = $stmt->get_result();
$articles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Articles</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Recent Articles</h2>

    <form method="get" action="view_articles.php" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by title..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($articles): ?>
        <ul>
            <?php foreach ($articles as $article): ?>
                <li style="margin-bottom: 20px;">
                    <h3>
                        <a href="view_article_details.php?id=<?= $article['id'] ?>">
                            <?= htmlspecialchars($article['title']) ?>
                        </a>
                    </h3>
                    <p><strong>Author:</strong> <?= htmlspecialchars($article['author']) ?></p>
                    <p><strong>Date:</strong> <?= date('F j, Y', strtotime($article['article_created_date'])) ?></p>
                    <p><?= nl2br(htmlspecialchars($article['summary'])) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No articles found.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
