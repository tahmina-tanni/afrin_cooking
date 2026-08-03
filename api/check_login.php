<?php
// api/check_login.php - Check user login status
require_once '../utils/functions.php';
require_once '../utils/ResponseFactory.php';

if (isLoggedIn()) {
    ResponseFactory::success([
        'loggedIn' => true,
        'userId' => $_SESSION['user_id'],
        'userName' => $_SESSION['user_name']
    ]);
} else {
    ResponseFactory::success(['loggedIn' => false]);
}
?>