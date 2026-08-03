<?php
// api/newsletter.php - Newsletter subscription API
require_once '../config/Database.php';
require_once '../utils/ResponseFactory.php';

$conn = Database::getInstance();

// Get POST data
$postData = json_decode(file_get_contents('php://input'), true);
$email = isset($postData['email']) ? $postData['email'] : '';

// Validate email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ResponseFactory::error('Please provide a valid email address');
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO newsletter (email) VALUES (?) ON DUPLICATE KEY UPDATE email = email");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    ResponseFactory::success(['message' => 'Successfully subscribed to newsletter']);
} else {
    $error_msg = $conn->error;
    $stmt->close();
    $conn->close();
    ResponseFactory::error('Failed to subscribe: ' . $error_msg);
}
?>