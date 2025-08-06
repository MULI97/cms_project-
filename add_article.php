<?php
session_start();
require_once 'includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['usertype'], ['Super_User', 'Administrator', 'Author'])) {
    header('Location: index.php');
    exit();
}

// Handle form submission
$success_message = $error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author_id = $_SESSION['user_id'];

    if ($title === '' || $content === '') {
        $error_message = 'Both title and content are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO articles (title, content, author_id, article_created_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ssi", $title, $content, $author_id);
        if ($stmt->execute()) {
            $success_message = 'Article posted successfully.';
        } else {
            $error_message = 'Failed to post article.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Article</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Add New Article</h2>

    <?php if ($success_message): ?>
        <p class="success"><?= $success_message ?></p>
    <?php elseif ($error_message): ?>
        <p class="error"><?= $error_message ?></p>
    <?php endif; ?>

    <form method="POST" action="add_article.php">
        <div class="form-group">
            <label for="title">Article Title:</label>
            <input type="text" name="title" id="title" class="form-control" required maxlength="255">
        </div>

        <div class="form-group">
            <label for="content">Article Content:</label>
            <textarea name="content" id="content" class="form-control" rows="10" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Publish</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
