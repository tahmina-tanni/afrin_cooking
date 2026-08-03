<?php
// api/delete_recipe.php - Delete recipe API
require_once '../config/Database.php';
require_once '../utils/functions.php';
require_once '../utils/ResponseFactory.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ResponseFactory::error('Method not allowed', 405);
}

// Check if user is logged in
if (!isLoggedIn()) {
    ResponseFactory::error('Please login to delete recipes', 401);
}

// Get the POST data
$postData = json_decode(file_get_contents('php://input'), true);
$recipeId = isset($postData['recipe_id']) ? (int)$postData['recipe_id'] : 0;
$userId = $_SESSION['user_id'];

// Validate data
if ($recipeId <= 0) {
    ResponseFactory::error('Invalid recipe ID');
}

$conn = Database::getInstance();

// Check if the user is the author or an admin
$stmt = $conn->prepare("SELECT user_id FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    ResponseFactory::error('Recipe not found', 404);
}

$recipe = $result->fetch_assoc();

// Check if the current user is the recipe author or an admin
if ($recipe['user_id'] != $userId && !isAdmin()) {
    $stmt->close();
    $conn->close();
    ResponseFactory::error('You do not have permission to delete this recipe', 403);
}

// First delete reviews associated with the recipe
$deleteReviewsStmt = $conn->prepare("DELETE FROM reviews WHERE recipe_id = ?");
$deleteReviewsStmt->bind_param("i", $recipeId);
$deleteReviewsStmt->execute();
$deleteReviewsStmt->close();

// Now delete the recipe itself
$deleteRecipeStmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
$deleteRecipeStmt->bind_param("i", $recipeId);

if ($deleteRecipeStmt->execute()) {
    // If there was an image, delete it
    $imageStmt = $conn->prepare("SELECT image FROM recipes WHERE id = ?");
    $imageStmt->bind_param("i", $recipeId);
    $imageStmt->execute();
    $imageResult = $imageStmt->get_result();
    
    if ($imageResult->num_rows > 0) {
        $imageData = $imageResult->fetch_assoc();
        $imagePath = $imageData['image'];
        
        if ($imagePath && file_exists('../' . $imagePath)) {
            unlink('../' . $imagePath);
        }
    }
    
    $stmt->close();
    $deleteRecipeStmt->close();
    $conn->close();
    
    ResponseFactory::success(['message' => 'Recipe deleted successfully']);
} else {
    $stmt->close();
    $deleteRecipeStmt->close();
    $conn->close();
    
    ResponseFactory::error('Failed to delete recipe');
}

// Helper function to check if user is admin
function isAdmin() {
    // You can implement proper admin check here
    // For now, just return false
    return false;
}
?>