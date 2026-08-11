<?php
session_start();
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

    $email = trim($_POST['email'] ?? '');

    $stmt = $conn->prepare("SELECT id, first_name, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        $otp_code = rand(100000, 999999);
        $otp_expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $updateStmt = $conn->prepare("UPDATE users SET otp_code=?, otp_expires_at=? WHERE id=?");
        $updateStmt->bind_param("ssi", $otp_code, $otp_expires_at, $user['id']);
        $updateStmt->execute();

        // Exact Mailer setup used in process-signup[cite: 2]
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();                                      
            $mail->Host       = 'smtp.gmail.com';                 
            $mail->SMTPAuth   = true;                             
            $mail->Username   = 'sashindeemantha@gmail.com';           
            $mail->Password   = 'mkht icqd cbjv cyon';              
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   
            $mail->Port       = 587;                              

            $mail->setFrom('sashindeemantha@gmail.com', 'SAPSRI Admin Portal');
            $mail->addAddress($email, $user['first_name']);
            $mail->addEmbeddedImage('../../assets/img/sapsri-logo-white.png', 'sapsri_logo');

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            
            // Reusing the exact HTML table structure[cite: 2]
            $mail->Body = "
            <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f8f9fa; padding: 40px 0; font-family: -apple-system, sans-serif;'>
            <tr><td align='center'>
                <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #eaeaea;'>
                    <tr><td align='center' style='background-color: #A20A35; padding: 30px 20px;'>
                        <img src='cid:sapsri_logo' alt='SAPSRI Logo' height='40' style='display: block; border: 0;'>
                    </td></tr>
                    <tr><td style='padding: 40px 40px 30px 40px; text-align: center;'>
                        <h2 style='margin: 0 0 15px 0; font-size: 22px; color: #1a1a1a; font-weight: 600;'>Reset Your Password</h2>
                        <p style='margin: 0 0 25px 0; font-size: 15px; color: #4a4a4a; line-height: 1.6;'>
                        Hello <strong>{$user['first_name']}</strong>,<br><br>
                        We received a request to reset the password for your SAPSRI Admin account. Enter this code to proceed:
                        </p>
                        <div style='background-color: #f4f5f6; border-radius: 8px; padding: 25px; margin-bottom: 25px;'>
                        <span style='font-family: monospace, sans-serif; font-size: 36px; font-weight: 700; color: #1a1a1a; letter-spacing: 8px;'>$otp_code</span>
                        </div>
                    </td></tr>
                </table>
            </td></tr>
            </table>";

            $mail->send();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        // Return success even if email doesn't exist to prevent email enumeration attacks
        echo json_encode(['status' => 'success']);
    }
}
?>