<?php
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $db = new DatabaseConnection();
    $conn = $db->getConnection();

    // Prevent SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE User_Name = ? AND UserType = 'Super_User' LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['Password'])) {
            $_SESSION['user_id'] = $user['userId'];
            $_SESSION['username'] = $user['User_Name'];
            $_SESSION['usertype'] = $user['UserType'];
            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['error'] = "Incorrect password.";
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Super_User not found.";
        header("Location: index.php");
        exit;
    }

    $stmt->close();
    $db->closeConnection();
}
?>
