<?php
require_once 'connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $inputPassword = $_POST['password'];

    $db = new DatabaseConnection();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT userId, User_Name, Password, UserType FROM users WHERE User_Name = ? AND UserType = 'Super_User'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ✅ Use password_verify to match hashed password
        if (password_verify($inputPassword, $user['Password'])) {
            $_SESSION['userId'] = $user['userId'];
            $_SESSION['username'] = $user['User_Name'];
            $_SESSION['userType'] = $user['UserType'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found or not a Super_User.";
    }

    $stmt->close();
    $db->closeConnection();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Super_User Sign-In</title>
</head>
<body>
    <h2>Super_User Sign-In</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Sign In</button>
    </form>
</body>
</html>
