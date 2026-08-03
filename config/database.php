<?php
// config/Database.php - Singleton Pattern (Afrin's Cooking)

class Database {
    private static $instance = null;
    private $conn;

    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_NAME = 'afrin_cooking';

    // Constructor private — bairer theke direct new Database() kora jabe na
    private function __construct() {
        $this->conn = new mysqli(self::DB_HOST, self::DB_USER, self::DB_PASS);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Create database if not exists
        $this->conn->query("CREATE DATABASE IF NOT EXISTS " . self::DB_NAME);
        $this->conn->select_db(self::DB_NAME);
    }

    // Single access point — connection ekbar e banbe, baki shob request e reuse hobe
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->initTables();
        }
        return self::$instance->conn;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize a singleton.");
    }

    // Initialize tables (runs only once, first time instance is created)
    private function initTables() {
        $conn = $this->conn;

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
    }
}
?>