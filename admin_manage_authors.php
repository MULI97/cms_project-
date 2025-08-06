<?php
session_start();
require_once 'dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header('Location: index.php');
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $author_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'Author'");
    $stmt->bind_param("i", $author_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_manage_authors.php");
    exit();
}

// Handle Edit (load data)
$edit_author = null;
if (isset($_GET['edit'])) {
    $author_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'Author'");
    $stmt->bind_param("i", $author_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_author = $result->fetch_assoc();
    $stmt->close();
}

// Handle Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (isset($_POST['update_id'])) {
        // Update author
        $update_id = intval($_POST['update_id']);
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, username=?, password=? WHERE id=? AND role='Author'");
            $stmt->bind_param("ssssi", $name, $email, $username, $hashed, $update_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, username=? WHERE id=? AND role='Author'");
            $stmt->bind_param("sssi", $name, $email, $username, $update_id);
        }
        $stmt->execute();
        $stmt->close();
    } else {
        // Add author
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, username, password, role) VALUES (?, ?, ?, ?, 'Author')");
        $stmt->bind_param("ssss", $name, $email, $username, $hashed);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin_manage_authors.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Authors</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'topnav.php'; ?>
    <div class="container">
        <h2>Manage Authors</h2>

        <!-- Add/Edit Author Form -->
        <div class="form-container">
            <h3><?= $edit_author ? "Edit Author" : "Add New Author" ?></h3>
            <form method="post">
                <input type="hidden" name="update_id" value="<?= $edit_author['id'] ?? '' ?>">
                <input type="text" name="name" placeholder="Full Name" value="<?= $edit_author['name'] ?? '' ?>" required><br>
                <input type="email" name="email" placeholder="Email" value="<?= $edit_author['email'] ?? '' ?>" required><br>
                <input type="text" name="username" placeholder="Username" value="<?= $edit_author['username'] ?? '' ?>" required><br>
                <input type="password" name="password" placeholder="<?= $edit_author ? 'Leave blank to keep old password' : 'Password' ?>"><br>
                <button type="submit"><?= $edit_author ? "Update Author" : "Add Author" ?></button>
            </form>
        </div>

        <!-- List Authors -->
        <h3>All Authors</h3>
        <table border="1" cellpadding="10" class="table-list">
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Username</th><th>Actions</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM users WHERE role='Author' ORDER BY id DESC");
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this author?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
