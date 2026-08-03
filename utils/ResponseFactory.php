<?php
// utils/ResponseFactory.php - Factory Pattern (Afrin's Cooking)
// Success/failure response shobshomoy same shape e return korbe

class ResponseFactory {

    // Success response — extra data (recipes, message, etc.) merge kore dibe
    public static function success($data = []) {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => true], $data));
        exit;
    }

    // Error response — status code shoho
    public static function error($message, $statusCode = 400) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
        exit;
    }
}
?>