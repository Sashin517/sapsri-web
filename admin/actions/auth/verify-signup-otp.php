<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    Database::setUpConnection();
    $conn = Database::$connection;

    $email = trim($_POST['email'] ?? '');
    $otp_code = trim($_POST['otp_code'] ?? '');

    if (empty($email) || empty($otp_code)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and OTP are required.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, otp_code, otp_expires_at, status FROM users WHERE email = ? AND status = 'pending_otp'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $current_time = date('Y-m-d H:i:s');

        // Check if OTP matches and is not expired
        if ($user['otp_code'] === $otp_code) {
            if ($user['otp_expires_at'] >= $current_time) {
                
                // OTP is valid. Clear OTP fields and set status to pending_approval
                $updateStmt = $conn->prepare("UPDATE users SET status = 'pending_approval', otp_code = NULL, otp_expires_at = NULL WHERE id = ?");
                $updateStmt->bind_param("i", $user['id']);
                
                if ($updateStmt->execute()) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'This verification code has expired. Please request a new one.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid verification code.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Session invalid or account already verified.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>