<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header('Location: index.php');
    exit();
}

$adminId = $_SESSION['user_id'];
$message = '';

// Fetch current admin details
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name']);
    $newEmail = trim($_POST['email']);
    $newPassword = trim($_POST['password']);

    if (!empty($newName) && !empty($newEmail)) {
        if (!empty($newPassword)) {
            // Update with new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $newName, $newEmail, $hashedPassword, $adminId);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $newName, $newEmail, $adminId);
        }

        if ($stmt->execute()) {
            $message = "Profile updated successfully.";
            $name = $newName;
            $email = $newEmail;
        } else {
            $message = "Error updating profile.";
        }
        $stmt->close();
    } else {
        $message = "Name and Email cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update My Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'topnav.php'; ?>
    <div class="container">
        <h2>Update My Profile</h2>
        <p style="color:green;"><?php echo $message; ?></p>
        <form method="post" action="">
            <label>Name:</label><br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>

            <label>New Password (leave blank to keep current):</label><br>
            <input type="password" name="password"><br><br>

            <button type="submit">Update Profile</button>
        </form>
        <br>
        <a href="dashboard_admin.php">← Back to Dashboard</a>
    </div>
</body>
</html>
