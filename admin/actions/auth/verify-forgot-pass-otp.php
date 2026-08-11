<?php
session_start();
require_once '../../../includes/connection.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    Database::setUpConnection();
    $conn = Database::$connection;

    $email = trim($_POST['email'] ?? '');
    $otp_code = trim($_POST['otp_code'] ?? '');

    $stmt = $conn->prepare("SELECT id, otp_code, otp_expires_at FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $current_time = date('Y-m-d H:i:s');

        if ($user['otp_code'] === $otp_code && $user['otp_expires_at'] >= $current_time) {
            // OTP is valid. Store a secure session flag to allow password reset
            $_SESSION['reset_authorized_email'] = $email;
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or expired verification code.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid verification code.']);
    }
}
?>