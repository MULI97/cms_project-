<?php
session_start();
require_once 'dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Author') {
    header("Location: index.php");
    exit();
}

$authorId = $_SESSION['user_id'];
$message = '';

// Handle Add Article
if (isset($_POST['add_article'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $date = date('Y-m-d H:i:s');

    $sql = "INSERT INTO articles (author_id, article_title, article_content, article_created_date)
            VALUES ('$authorId', '$title', '$content', '$date')";
    if (mysqli_query($conn, $sql)) {
        $message = "Article added successfully.";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $check = mysqli_query($conn, "SELECT * FROM articles WHERE article_id='$deleteId' AND author_id='$authorId'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM articles WHERE article_id='$deleteId'");
        $message = "Article deleted.";
    } else {
        $message = "Unauthorized deletion attempt.";
    }
}

// Handle Update
if (isset($_POST['update_article'])) {
    $id = $_POST['article_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    $check = mysqli_query($conn, "SELECT * FROM articles WHERE article_id='$id' AND author_id='$authorId'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE articles SET article_title='$title', article_content='$content' WHERE article_id='$id'");
        $message = "Article updated.";
    } else {
        $message = "Unauthorized update attempt.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Manage My Articles</title>
</head>
<body>
    <h2>Manage My Articles</h2>
    <p style="color: green;"><?php echo $message; ?></p>

    <!-- Add New Article -->
    <h3>Add New Article</h3>
    <form method="POST" action="">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>
        <label>Content:</label><br>
        <textarea name="content" rows="5" cols="40" required></textarea><br><br>
        <input type="submit" name="add_article" value="Add Article">
    </form>

    <hr>

    <!-- View and Edit Own Articles -->
    <h3>My Articles</h3>
    <?php
    $result = mysqli_query($conn, "SELECT * FROM articles WHERE author_id='$authorId' ORDER BY article_created_date DESC");
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <form method="POST" action="">
            <input type="hidden" name="article_id" value="<?php echo $row['article_id']; ?>">
            <label>Title:</label><br>
            <input type="text" name="title" value="<?php echo htmlspecialchars($row['article_title']); ?>" required><br><br>
            <label>Content:</label><br>
            <textarea name="content" rows="5" cols="40" required><?php echo htmlspecialchars($row['article_content']); ?></textarea><br><br>
            <input type="submit" name="update_article" value="Update">
            <a href="?delete=<?php echo $row['article_id']; ?>" onclick="return confirm('Delete this article?');">Delete</a>
        </form>
        <hr>
    <?php } ?>

    <p><a href="dashboard_author.php">← Back to Dashboard</a></p>
</body>
</html>
