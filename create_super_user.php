<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../connection.php');


$db = new DatabaseConnection();
$conn = $db->getConnection();

// Super_User account details
$fullName = "System Superuser";
$email = "superuser@example.com";
$phoneNumber = "0712345678";
$username = "superadmin";
$password = "SuperSecure123";  // You can change this
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$userType = "Super_User";
$accessTime = date("Y-m-d H:i:s");
$profileImage = "default.png";  // Optional — change as needed
$address = "HQ, Nairobi";

$stmt = $conn->prepare("INSERT INTO users (Full_Name, email, phone_Number, User_Name, Password, UserType, AccessTime, profile_Image, Address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $fullName, $email, $phoneNumber, $username, $hashedPassword, $userType, $accessTime, $profileImage, $address);

if ($stmt->execute()) {
    echo "✅ Super_User account created successfully.<br>";
    echo "➡ Username: <strong>$username</strong><br>";
    echo "➡ Password: <strong>$password</strong>";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$db->closeConnection();
?>
