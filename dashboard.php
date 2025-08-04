<?php
// dashboard.php

session_start();

// Redirect if user is not logged in or not Super_User
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'Super_User') {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Super_User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            padding: 50px;
        }
        .container {
            width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        .welcome {
            margin-top: 20px;
            font-size: 18px;
        }
        form {
            margin-top: 30px;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .logout-btn:hover {
            background: #b52a36;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome to the Super_User Dashboard</h2>
    <div class="welcome">
        Hello, <strong><?= htmlspecialchars($username) ?></strong>!
    </div>

    <!-- Secure logout via POST -->
    <form method="POST" action="logout.php">
        <button type="submit" class="logout-btn">Log Out</button>
    </form>
</div>

</body>
</html>
