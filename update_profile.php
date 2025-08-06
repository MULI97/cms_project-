<?php
// update_profile.php

session_start();
require_once 'connection.php';

// Redirect if not logged in or not Super_User
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'Super_User') {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch current user details
$query = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newName = trim($_POST['name']);
    $newEmail = trim($_POST['email']);
    $newPassword = trim($_POST['password']);

    if (empty($newName) || empty($newEmail)) {
        $error = "Name and Email cannot be empty.";
    } else {
        // Update query base
        $query = "UPDATE users SET name = ?, email = ?";
        $params = [$newName, $newEmail];
        $types = "ss";

        // Include password update only if filled
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $query .= ", password = ?";
            $params[] = $hashedPassword;
            $types .= "s";
        }

        $query .= " WHERE id = ?";
        $params[] = $userId;
        $types .= "i";

        // Prepare & execute
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            // Update session email if changed
            $_SESSION['email'] = $newEmail;
        } else {
            $error = "Error updating profile. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update My Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h2>Update My Profile</h2>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">Full Name:</label><br>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br><br>

        <label for="email">Email Address:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br><br>

        <label for="password">New Password (leave blank to keep current):</label><br>
        <input type="password" id="password" name="password"><br><br>

        <button type="submit" class="btn">Update Profile</button>
        <a href="dashboard.php" class="btn">Cancel</a>
    </form>
</div>

</body>
</html>
