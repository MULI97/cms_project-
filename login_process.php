<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'connection.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $db_username, $db_password, $role);
            $stmt->fetch();

            if (password_verify($password, $db_password)) {
                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = $db_username;
                $_SESSION["role"] = $role;

                // Redirect based on role
                if ($role == "Super_User") {
                    header("Location: dashboard_super.php");
                    exit;
                } elseif ($role == "Administrator") {
                    header("Location: dashboard_admin.php");
                    exit;
                } elseif ($role == "Author") {
                    header("Location: dashboard_author.php");
                    exit;
                } else {
                    $error = "Unknown user role.";
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    }
}
?>

<!-- LOGIN FORM UI -->
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'components/topnav.php'; ?>
    <div class="content-form">
        <h2>Sign In</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post" action="login.php">
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required><br>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
