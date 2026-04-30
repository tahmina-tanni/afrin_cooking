<?php
require_once '../config/database.php';
require_once '../utils/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getRecipes();
        break;

    case 'POST':
        if (isLoggedIn()) {
            addRecipe();
        } else {
            echo json_encode(['success' => false, 'message' => 'Please login to submit a recipe']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}


// ================= GET RECIPES =================
function getRecipes() {
    $conn = connectDB();

    $featured = isset($_GET['featured']) ? (int)$_GET['featured'] : 0;
    $popular = isset($_GET['popular']) ? (int)$_GET['popular'] : 0;
    $category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
    $search = isset($_GET['q']) ? $_GET['q'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
    $page = isset($_GET['page']) ? (int)$page = $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // FIXED QUERY (author removed)
    $query = "SELECT r.id, r.title, r.description, r.image, r.created_at,
              c.name as category,
              (SELECT COUNT(*) FROM reviews WHERE recipe_id = r.id) as reviews,
              (SELECT AVG(rating) FROM reviews WHERE recipe_id = r.id) as rating
              FROM recipes r
              JOIN categories c ON r.category_id = c.id
              WHERE 1=1";

    if ($featured) {
        $query .= " AND r.featured = 1";
    }

    if ($category_id) {
        $query .= " AND r.category_id = $category_id";
    }

    if ($search) {
        $search = $conn->real_escape_string($search);
        $query .= " AND (r.title LIKE '%$search%' OR r.description LIKE '%$search%')";
    }

    if ($popular) {
        $query .= " ORDER BY rating DESC, reviews DESC";
    } else {
        $query .= " ORDER BY r.created_at DESC";
    }

    $query .= " LIMIT $offset, $limit";

    $result = $conn->query($query);
    $recipes = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['rating'] = isset($row['rating']) ? round($row['rating'] * 2) / 2 : 0;
            $recipes[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'recipes' => $recipes
    ]);

    $conn->close();
}


// ================= ADD RECIPE =================
function addRecipe() {
    $conn = connectDB();

    $title = isset($_POST['title']) ? sanitizeInput($_POST['title']) : '';
    $category_id = isset($_POST['category']) ? (int)$_POST['category'] : 0;
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
    $ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : '';
    $steps = isset($_POST['steps']) ? $_POST['steps'] : '';
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($category_id) || empty($ingredients) || empty($steps)) {
        echo json_encode([
            'success' => false,
            'message' => 'Title, category, ingredients and steps are required'
        ]);
        return;
    }

    // ================= IMAGE UPLOAD =================
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        $target_dir = __DIR__ . "/../uploads/recipes/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'uploads/recipes/' . $file_name;
        } else {
            echo json_encode(['success' => false, 'message' => 'Image upload failed']);
            return;
        }
    }

    $featured = 0;

    $stmt = $conn->prepare("INSERT INTO recipes (title, description, ingredients, steps, image, user_id, category_id, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiis", $title, $description, $ingredients, $steps, $image_path, $user_id, $category_id, $featured);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Recipe added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>