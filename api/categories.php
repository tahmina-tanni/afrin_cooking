<?php
// api/categories.php - Categories API
require_once '../config/Database.php';
require_once '../utils/ResponseFactory.php';

// Get all categories
$conn = Database::getInstance();

$query = "SELECT c.id, c.name, c.image, COUNT(r.id) as count 
          FROM categories c 
          LEFT JOIN recipes r ON c.id = r.category_id 
          GROUP BY c.id 
          ORDER BY c.name";

$result = $conn->query($query);
$categories = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$conn->close();

ResponseFactory::success(['categories' => $categories]);
?>