<?php
session_start();
require_once '../model/user_model.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    
    $userData = [
        'firstName' => $_POST['firstName'] ?? '',
        'lastName' => $_POST['lastName'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? ''
    ];
    
    // Handle password update if provided
    if (!empty($_POST['newPassword'])) {
        $userData['currentPassword'] = $_POST['currentPassword'] ?? '';
        $userData['newPassword'] = $_POST['newPassword'];
    }
    
    $result = updateCurrentUserProfile($username, $userData);
    
    if ($result === "success") {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
    } else if ($result === "invalid_password") {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

