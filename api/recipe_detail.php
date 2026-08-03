<?php
// api/recipe_detail.php - Single recipe details API
require_once '../config/Database.php';
require_once '../utils/functions.php';
require_once '../utils/ResponseFactory.php';

// Get recipe ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    ResponseFactory::error('Invalid recipe ID');
}

$conn = Database::getInstance();

// Get recipe details with joins to get category and author names
$query = "SELECT r.*, c.name as category,
          (SELECT COUNT(*) FROM reviews WHERE recipe_id = r.id) as reviews,
          (SELECT AVG(rating) FROM reviews WHERE recipe_id = r.id) as rating
          FROM recipes r
          JOIN categories c ON r.category_id = c.id
          WHERE r.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $recipe = $result->fetch_assoc();
    
    // Format the rating to one decimal place
    if (isset($recipe['rating'])) {
        $recipe['rating'] = round($recipe['rating'] * 2) / 2; // Round to nearest 0.5
    } else {
        $recipe['rating'] = 0;
    }
    
    $stmt->close();
    $conn->close();
    ResponseFactory::success(['recipe' => $recipe]);
} else {
    $stmt->close();
    $conn->close();
    ResponseFactory::error('Recipe not found', 404);
}
?>