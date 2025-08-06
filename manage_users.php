<?php
// manage_users.php

session_start();
require_once 'connection.php';

// Redirect if not logged in or not Super_User
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'Super_User') {
    header('Location: index.php');
    exit();
}

// Handle new user addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $usertype = $_POST['usertype'];
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, usertype, username, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $usertype, $username, $password);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Prevent deleting self
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage_users.php");
    exit();
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $usertype = $_POST['usertype'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, usertype = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $usertype, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, usertype = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $usertype, $id);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: manage_users.php");
    exit();
}

// Fetch all users except the Super_User currently logged in
$stmt = $conn->prepare("SELECT * FROM users WHERE id != ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h2>Manage Other Users</h2>

    <h3>Add New User</h3>
    <form method="POST">
        <input type="hidden" name="add_user" value="1">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="username" placeholder="Username" required><br>
        <select name="usertype" required>
            <option value="Administrator">Administrator</option>
            <option value="Author">Author</option>
        </select><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Add User</button>
    </form>

    <hr>

    <h3>All Users</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Usertype</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
        <?php while ($user = $users->fetch_assoc()): ?>
            <tr>
                <form method="POST">
                    <td><input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"></td>
                    <td><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"></td>
                    <td>
                        <select name="usertype">
                            <option value="Administrator" <?= $user['usertype'] === 'Administrator' ? 'selected' : '' ?>>Administrator</option>
                            <option value="Author" <?= $user['usertype'] === 'Author' ? 'selected' : '' ?>>Author</option>
                        </select>
                    </td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td>
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="update_user" value="1">
                        <input type="password" name="password" placeholder="New Password (optional)">
                        <button type="submit">Update</button>
                        <a href="manage_users.php?delete=<?= $user['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="dashboard.php">← Back to Dashboard</a></p>
</div>

</body>
</html>
