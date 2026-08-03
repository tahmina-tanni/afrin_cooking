<?php
session_start();
require_once '../config/Database.php';
require_once '../utils/ResponseFactory.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    ResponseFactory::error('You must be logged in to feature a recipe', 401);
}

// Get the request body
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['recipe_id'])) {
    ResponseFactory::error('Recipe ID is required');
}

// Connect to the database
$conn = Database::getInstance();

// Check if the user owns this recipe
$stmt = $conn->prepare("SELECT user_id FROM recipes WHERE id = ?");
$stmt->bind_param("i", $data['recipe_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    ResponseFactory::error('Recipe not found', 404);
}

$recipe = $result->fetch_assoc();

// Check if user is owner
if ($recipe['user_id'] != $_SESSION['user_id']) {
    ResponseFactory::error('You can only feature your own recipes', 403);
}

// Mark recipe as featured
$stmt = $conn->prepare("UPDATE recipes SET featured = 1 WHERE id = ?");
$stmt->bind_param("i", $data['recipe_id']);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    ResponseFactory::success(['message' => 'Recipe successfully featured']);
} else {
    $error_msg = $conn->error;
    $stmt->close();
    $conn->close();
    ResponseFactory::error('Failed to feature recipe: ' . $error_msg);
}
?>