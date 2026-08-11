<?php
session_start();
require_once '../../../includes/connection.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    Database::setUpConnection();
    $conn = Database::$connection;

    $password = $_POST['password'] ?? '';
    $email = $_SESSION['reset_authorized_email'] ?? '';

    if (empty($password) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized request.']);
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $updateStmt = $conn->prepare("UPDATE users SET password_hash = ?, otp_code = NULL, otp_expires_at = NULL WHERE email = ?");
    $updateStmt->bind_param("ss", $password_hash, $email);
    
    if ($updateStmt->execute()) {
        unset($_SESSION['reset_authorized_email']); // Clear security flag
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update password.']);
    }
}
?>