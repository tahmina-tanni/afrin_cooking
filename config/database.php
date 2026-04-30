<?php
// config/database.php - Clean Setup Version (Afrin's Cooking)

// Database Config
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'afrin_cooking');

// Create connection + database
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database if not exists
    $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);

    // Select DB
    $conn->select_db(DB_NAME);

    return $conn;
}

// Initialize tables
function initTables() {
    $conn = connectDB();

    // USERS
    $conn->query("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    // CATEGORIES
    $conn->query("
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    // RECIPES
    $conn->query("
        CREATE TABLE IF NOT EXISTS recipes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            description TEXT,
            ingredients TEXT NOT NULL,
            steps TEXT NOT NULL,
            image VARCHAR(255),
            user_id INT NOT NULL,
            category_id INT NOT NULL,
            featured TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    // REVIEWS
    $conn->query("
        CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            recipe_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL,
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    // NEWSLETTER
    $conn->query("
        CREATE TABLE IF NOT EXISTS newsletter (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL UNIQUE,
            subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");

    // Default Categories
    $categories = [
        ['Breakfast', 'uploads/categories/breakfast.jpg'],
        ['Lunch', 'uploads/categories/lunch.jpg'],
        ['Dinner', 'uploads/categories/dinner.jpg'],
        ['Dessert', 'uploads/categories/dessert.jpg'],
        ['Vegetarian', 'uploads/categories/vegetarian.jpg'],
        ['Vegan', 'uploads/categories/vegan.jpg'],
        ['Quick & Easy', 'uploads/categories/quick.jpg']
    ];

    foreach ($categories as $cat) {
        $stmt = $conn->prepare("INSERT IGNORE INTO categories (name, image) VALUES (?, ?)");
        $stmt->bind_param("ss", $cat[0], $cat[1]);
        $stmt->execute();
    }

    $conn->close();
}

// AUTO RUN SETUP (IMPORTANT)
initTables();
?>