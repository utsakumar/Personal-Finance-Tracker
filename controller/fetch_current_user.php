<?php
session_start();
require_once '../model/user_model.php';

header('Content-Type: application/json');

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user = getUserByUsername($username);
    
    if ($user) {
        // Add default values for preferences if they don't exist in database
        // These can be stored in localStorage on frontend or added to database later
        $user['u_phone'] = $user['u_phone'] ?? '';
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
}
?>

