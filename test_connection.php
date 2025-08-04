<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'connection.php';

$db = new DatabaseConnection();
$conn = $db->getConnection();

// Now you can use $conn to run queries

// Example: Check if the connection works
echo "Database connected successfully!";
?>
