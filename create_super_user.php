<?php
// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to database
require_once '../connection.php'; // Adjusted path if necessary

$db = new DatabaseConnection();
$conn = $db->getConnection();

// Define Super_User account details
$fullName     = "System Superuser";
$email        = "superuser@example.com";
$phoneNumber  = "0712345678";
$username     = "superadmin";
$password     = "SuperSecure123"; // This is the plain password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Securely hash it
$userType     = "Super_User";
$accessTime   = date("Y-m-d H:i:s");
$profileImage = "default.png";
$address      = "HQ, Nairobi";

// Prepare and execute INSERT statement
$stmt = $conn->prepare("INSERT INTO users 
    (Full_Name, email, phone_Number, User_Name, Password, UserType, AccessTime, profile_Image, Address) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", 
    $fullName, $email, $phoneNumber, $username, $hashedPassword, 
    $userType, $accessTime, $profileImage, $address);

if ($stmt->execute()) {
    echo "✅ Super_User account created successfully.<br>";
    echo "➡ Username: <strong>$username</strong><br>";
    echo "➡ Password: <strong>$password</strong><br>";
    echo "🔐 Hashed Password (stored): <code>$hashedPassword</code>";
} else {
    echo "❌ Error creating Super_User: " . $stmt->error;
}

// Cleanup
$stmt->close();
$db->closeConnection();
?>
