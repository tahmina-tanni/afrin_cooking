<?php
// api/auth.php - User authentication API
require_once '../config/Database.php';
require_once '../utils/ResponseFactory.php';
session_start();

// Handle API requests
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'login':
        login();
        break;
    case 'register':
        register();
        break;
    case 'logout':
// Inside the switch statement, make sure the logout case is handled:
    case 'logout':
        // Destroy session
        session_destroy();
        
        // Return success response
        ResponseFactory::success(['message' => 'Logout successful']);
        break;

    default:
        ResponseFactory::error('Invalid action');
}

// Login function
function login() {
    $conn = Database::getInstance();
    
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate input
    if (empty($email) || empty($password)) {
        ResponseFactory::error('Email and password are required');
    }
    
    // Prepare statement
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            ResponseFactory::success(['message' => 'Login successful']);
        } else {
            ResponseFactory::error('Invalid password');
        }
    } else {
        ResponseFactory::error('User not found');
    }
    
    $stmt->close();
    $conn->close();
}

// Register function
function register() {
    $conn = Database::getInstance();
    
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // Validate input
    if (empty($name) || empty($email) || empty($password)) {
        ResponseFactory::error('All fields are required');
    }
    
    if ($password !== $confirm_password) {
        ResponseFactory::error('Passwords do not match');
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        ResponseFactory::error('Email already in use');
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        ResponseFactory::success(['message' => 'Registration successful']);
    } else {
        $error_msg = $conn->error;
        $stmt->close();
        $conn->close();
        ResponseFactory::error('Registration failed: ' . $error_msg);
    }
}

// Logout function
function logout() {
    // Destroy session
    session_destroy();
    
    ResponseFactory::success(['message' => 'Logout successful']);
}
?>