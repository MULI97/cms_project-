<?php
session_start();
require_once 'connection.php';

// PHPMailer namespace and classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

// Redirect if not logged in or unauthorized
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['usertype'], ['Super_User', 'Administrator', 'Author'])) {
    header('Location: index.php');
    exit();
}

// Handle form submission
$success_message = $error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author_id = $_SESSION['user_id'];

    if ($title === '' || $content === '') {
        $error_message = 'Both title and content are required.';
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO articles (title, content, author_id, article_created_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ssi", $title, $content, $author_id);
        if ($stmt->execute()) {
            $success_message = 'Article posted successfully.';

            // Fetch administrator emails
            $result = $conn->query("SELECT email FROM users WHERE usertype IN ('Administrator', 'Super_User')");
            $adminEmails = [];
            while ($row = $result->fetch_assoc()) {
                $adminEmails[] = $row['email'];
            }

            // Send email to admins
            if (!empty($adminEmails)) {
                $mail = new PHPMailer(true);
                try {
                    // SMTP settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'mulipatience97@gmail.com'; 
                    $mail->Password = 'yhiiwysirrpuvje';   
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('mulipatience97@gmail.com', 'Article Notifier'); // sender
                    foreach ($adminEmails as $email) {
                        $mail->addAddress($email);
                    }

                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = 'New Article Posted';
                    $mail->Body = "
                        <h3>New Article Notification</h3>
                        <p><strong>Title:</strong> {$title}</p>
                        <p><strong>Content Preview:</strong></p>
                        <p>" . nl2br(substr($content, 0, 300)) . "...</p>
                        <p><a href='http://yourdomain.com/articles.php'>View All Articles</a></p>
                    ";

                    $mail->send();
                } catch (Exception $e) {
                    // You could optionally log this
                    error_log("Email failed: {$mail->ErrorInfo}");
                }
            }
        } else {
            $error_message = 'Failed to post article.';
        }
        $stmt->close();
    }
}
?>
