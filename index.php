<?php
require_once 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $inputPassword = $_POST['password'];

    $db = new DatabaseConnection();
    $conn = $db->getConnection();

    // Select user with valid role
    $stmt = $conn->prepare("SELECT userId, User_Name, Password, UserType FROM users WHERE User_Name = ? AND (UserType = 'Super_User' OR UserType = 'Administrator' OR UserType = 'Author')");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($inputPassword, $user['Password'])) {
            $_SESSION['userId'] = $user['userId'];
            $_SESSION['username'] = $user['User_Name'];
            $_SESSION['userType'] = $user['UserType'];

            // Redirect based on UserType
            switch ($user['UserType']) {
                case 'Super_User':
                    header("Location: dashboard_super.php");
                    break;
                case 'Administrator':
                    header("Location: dashboard_admin.php");
                    break;
                case 'Author':
                    header("Location: dashboard_author.php");
                    break;
                default:
                    $error = "Unauthorized role.";
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found or not authorized.";
    }

    $stmt->close();
    $db->closeConnection();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>System Sign-In</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>System Sign-In</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>
