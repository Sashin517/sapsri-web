<?php
session_start();
// Go up two levels (../actions/ -> ../sapsri-admin/) then into includes/
require_once '../../../includes/connection.php';
require_once '../../includes/PHPMailer/Exception.php';
require_once '../../includes/PHPMailer/PHPMailer.php';
require_once '../../includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    Database::setUpConnection();
    $conn = Database::$connection;

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $terms = isset($_POST['terms']) ? 1 : 0;

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $existingUser = $result->fetch_assoc();
        // If they exist but are still stuck in OTP phase, we will overwrite their OTP. 
        // If they are active/pending_approval, reject the signup.
        if ($existingUser['status'] !== 'pending_otp') {
            echo json_encode(['status' => 'error', 'message' => 'This email is already registered.']);
            exit;
        }
    }

    // Generate 6-digit OTP and Hash Password
    $otp_code = rand(100000, 999999);
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $otp_expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    if ($result->num_rows > 0) {
        // Update existing unverified record
        $updateStmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, password_hash=?, otp_code=?, otp_expires_at=? WHERE email=?");
        $updateStmt->bind_param("ssssss", $first_name, $last_name, $password_hash, $otp_code, $otp_expires_at, $email);
        $updateStmt->execute();
    } else {
        // Insert new record
        $insertStmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash, terms_agreed, status, otp_code, otp_expires_at) VALUES (?, ?, ?, ?, ?, 'pending_otp', ?, ?)");
        $insertStmt->bind_param("ssssiss", $first_name, $last_name, $email, $password_hash, $terms, $otp_code, $otp_expires_at);
        $insertStmt->execute();
    }

    // Send the OTP via Email
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com';                 // Specify main and backup SMTP servers (e.g., Gmail)
        $mail->SMTPAuth   = true;                             // Enable SMTP authentication
        $mail->Username   = 'sashindeemantha@gmail.com';           // Your Gmail address
        $mail->Password   = 'mkht icqd cbjv cyon';              // Your Gmail App Password (NOT your regular password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 587;                              // TCP port to connect to (587 for TLS, 465 for SSL)

        // Recipients
        $mail->setFrom('sashindeemantha@gmail.com', 'SAPSRI Admin Portal');
        $mail->addAddress($email, $first_name . ' ' . $last_name);
        $mail->addEmbeddedImage('../../assets/img/sapsri-logo-white.png', 'sapsri_logo');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your SAPSRI Admin Account';
        $mail->Body = "
            <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f8f9fa; padding: 40px 0; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
            <tr>
                <td align='center'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #eaeaea;'>
                    
                    <!-- Header for White Logo -->
                    <tr>
                    <td align='center' style='background-color: #A20A35; padding: 30px 20px;'>
                        <img src='cid:sapsri_logo' alt='SAPSRI Logo' height='40' style='display: block; border: 0;'>
                    </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                    <td style='padding: 40px 40px 30px 40px; text-align: center;'>
                        <h2 style='margin: 0 0 15px 0; font-size: 22px; color: #1a1a1a; font-weight: 600;'>Verify your SAPSRI sign-up</h2>
                        <p style='margin: 0 0 25px 0; font-size: 15px; color: #4a4a4a; line-height: 1.6;'>
                        Hello <strong>$first_name</strong>,<br><br>
                        We have received a sign-up attempt with the following code. Please enter it in the browser window where you started signing up for the SAPSRI Admin Portal.
                        </p>
                        
                        <!-- OTP Box -->
                        <div style='background-color: #f4f5f6; border-radius: 8px; padding: 25px; margin-bottom: 25px;'>
                        <span style='font-family: monospace, sans-serif; font-size: 36px; font-weight: 700; color: #1a1a1a; letter-spacing: 8px;'>$otp_code</span>
                        </div>
                        
                        <p style='margin: 0; font-size: 13px; color: #888888; line-height: 1.5;'>
                        If you did not attempt to sign up but received this email, please disregard it. The code will remain active for 15 minutes.
                        </p>
                    </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                    <td style='padding: 0 40px 30px 40px;'>
                        <hr style='border: 0; border-top: 1px solid #eaeaea; margin: 0 0 20px 0;'>
                        <p style='margin: 0 0 10px 0; font-size: 12px; color: #888888; text-align: center;'>
                        SAPSRI Admin Portal - Secure Content Management
                        </p>
                        <p style='margin: 0; font-size: 12px; color: #b0b0b0; text-align: center;'>
                        &copy; 2026 SL Devs. All rights reserved.
                        </p>
                    </td>
                    </tr>
                    
                </table>
                </td>
            </tr>
            </table>
            ";

        $mail->send();
        
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>