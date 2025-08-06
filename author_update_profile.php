<?php
session_start();
require 'dbConnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Author') {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];
$success = $error = "";
$name = $email = "";

// Fetch current details
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

// Handle update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = trim($_POST['name']);
    $newPassword = trim($_POST['password']);

    if (empty($newName)) {
        $error = "Name cannot be empty.";
    } else {
        // If password field is not empty, update both name and password
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssi", $newName, $hashedPassword, $userId);
        } else {
            // Only update name
            $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $newName, $userId);
        }

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            $name = $newName; // Update the name on screen
        } else {
            $error = "Error updating profile.";
        }

        $stmt->close();
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
<div class="container">
    <h2>Update My Profile</h2>

    <?php if ($success): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Full Name:</label><br>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>

        <label for="email">Email (cannot change):</label><br>
        <input type="email" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly><br><br>

        <label for="password">New Password (leave blank to keep existing):</label><br>
        <input type="password" name="password" id="password"><br><br>

        <input type="submit" value="Update Profile">
    </form>

    <br>
    <a href="dashboard_author.php" class="button">Back to Dashboard</a>
</div>
</body>
</html>
