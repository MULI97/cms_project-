<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// add_user.php

session_start();
require_once 'connection.php';

// Redirect if not logged in or not Super_User
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'Super_User') {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $usertype = $_POST['usertype'];

    // Validate
    if (empty($username) || empty($name) || empty($email) || empty($password) || empty($usertype)) {
        $error = 'All fields are required.';
    } else {
        // Check for existing username or email
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Username or Email already exists.';
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $insertStmt = $conn->prepare("INSERT INTO users (username, name, email, password, usertype) VALUES (?, ?, ?, ?, ?)");
            $insertStmt->bind_param("sssss", $username, $name, $email, $hashedPassword, $usertype);
            if ($insertStmt->execute()) {
                $success = "User added successfully!";
            } else {
                $error = "Failed to add user.";
            }
            $insertStmt->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Add New User</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="add_user.php">
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label>User Type:</label>
            <select name="usertype" class="form-control" required>
                <option value="">-- Select User Type --</option>
                <option value="Administrator">Administrator</option>
                <option value="Author">Author</option>
                <option value="Super_User">Super_User</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Add User</button>
        <a href="manage_users.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
