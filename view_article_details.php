<?php
// view_article_details.php

session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "No article selected.";
    exit();
}

$article_id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT a.title, a.content, a.article_created_date, u.name AS author
    FROM articles a
    JOIN users u ON a.user_id = u.id
    WHERE a.id = ?
");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Article not found.";
    exit();
}

$article = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2><?= htmlspecialchars($article['title']) ?></h2>
    <p><strong>Author:</strong> <?= htmlspecialchars($article['author']) ?></p>
    <p><strong>Published on:</strong> <?= date('F j, Y', strtotime($article['article_created_date'])) ?></p>
    <div style="margin-top: 20px;">
        <?= nl2br(htmlspecialchars($article['content'])) ?>
    </div>

    <a href="view_articles.php" class="btn btn-secondary" style="margin-top: 20px;">Back to Articles</a>
</div>
</body>
</html>
