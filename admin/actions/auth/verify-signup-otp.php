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

    $email = trim($_POST['email'] ?? '');
    $otp_code = trim($_POST['otp_code'] ?? '');

    if (empty($email) || empty($otp_code)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and OTP are required.']);
        exit;
    }

    // UPDATE: Grab first_name and last_name so we can use them in the admin email
    $stmt = $conn->prepare("SELECT id, first_name, last_name, otp_code, otp_expires_at, status FROM users WHERE email = ? AND status = 'pending_otp'");
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
                    
                    // ==========================================
                    // SEND NOTIFICATION TO ALL ACTIVE ADMINS
                    // ==========================================
                    $mail = new PHPMailer(true);
                    try {
                        // --- CPANEL SMTP CONFIGURATION ---
                        $mail->isSMTP();
                        
                        $mail->Host       = 'mail.sapsri.lk';                 
                        $mail->SMTPAuth   = true;                                   
                        $mail->Username   = 'noreply@sapsri.lk';              
                        $mail->Password   = 'S.b*JgSY.uV5Q]vs'; 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      
                        $mail->Port       = 465;                              

                        $mail->setFrom('noreply@sapsri.lk', 'SAPSRI Admin Portal');
                        $mail->addEmbeddedImage('../../assets/img/sapsri-logo-white.png', 'sapsri_logo');

                        
                        // Fetch all Active Super Admins (role_id = 1)
                        $adminQuery = "SELECT email FROM users WHERE status = 'active' AND role_id = 1";
                        $adminRes = $conn->query($adminQuery);

                        if ($adminRes && $adminRes->num_rows > 0) {
                            
                            // Add all admins as BCC so they don't see each other's email addresses
                            while ($adminRow = $adminRes->fetch_assoc()) {
                                $mail->addBCC($adminRow['email']);
                            }

                            $first_name = htmlspecialchars($user['first_name']);
                            $last_name = htmlspecialchars($user['last_name']);
                            $safe_email = htmlspecialchars($email);

                            $mail->isHTML(true);
                            $mail->Subject = 'New Access Request: SAPSRI Admin Portal';
                            $mail->Body = "
                                <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f3f4f6; padding: 40px 0; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
                                    <tr>
                                        <td align='center'>
                                            <!-- Main White Card -->
                                            <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 500px; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);'>
                                                
                                                <!-- Header -->
                                                <tr>
                                                    <td style='background-color: #A20A35; padding: 24px 30px;'>
                                                        <table width='100%' cellpadding='0' cellspacing='0'>
                                                            <tr>
                                                                <td align='left' valign='middle'>
                                                                    <span style='color: #ffffff; font-size: 20px; font-weight: 600; vertical-align: middle;'>SAPSRI System Alert</span>
                                                                </td>
                                                                <td align='right' valign='middle'>
                                                                    <img src='cid:sapsri_logo' alt='SAPSRI Logo' height='26' style='display: inline-block; vertical-align: middle; border: 0;'>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Body Content -->
                                                <tr>
                                                    <td style='padding: 40px 30px;'>
                                                        <p style='margin: 0 0 10px 0; font-size: 12px; font-weight: 700; color: #6b7280; letter-spacing: 1px; text-transform: uppercase;'>
                                                            Admin Notification
                                                        </p>
                                                        <h2 style='margin: 0 0 20px 0; font-size: 22px; color: #111827; font-weight: 700; line-height: 1.3;'>
                                                            New User Verification Complete
                                                        </h2>
                                                        <p style='margin: 0 0 25px 0; font-size: 15px; color: #4b5563; line-height: 1.6;'>
                                                            A new user has successfully verified their email and is requesting access to the SAPSRI Admin Portal.
                                                        </p>
                                                        
                                                        <!-- User Details Box -->
                                                        <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 25px;'>
                                                            <tr>
                                                                <td style='padding: 20px;'>
                                                                    <p style='margin: 0 0 12px 0; font-size: 15px; color: #111827;'>
                                                                        <strong style='color: #374151; font-weight: 600; display: inline-block; width: 60px;'>Name:</strong> $first_name $last_name
                                                                    </p>
                                                                    <p style='margin: 0; font-size: 15px; color: #111827;'>
                                                                        <strong style='color: #374151; font-weight: 600; display: inline-block; width: 60px;'>Email:</strong> <a href='mailto:$safe_email' style='color: #111827; text-decoration: none;'>$safe_email</a>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>

                                                        <p style='margin: 0 0 25px 0; font-size: 15px; color: #4b5563; line-height: 1.6;'>
                                                            Assign a role and <strong>approve</strong> their access in your <strong>User Management</strong> dashboard.
                                                        </p>
                                                        
                                                        <!-- Action Button -->
                                                        <table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom: 25px;'>
                                                            <tr>
                                                                <td align='center'>
                                                                    <a href='https://sapsri.lk/project-sedna/admin/index.php' style='display: block; width: 100%; background-color: #A20A35; color: #ffffff; text-decoration: none; padding: 14px 0; border-radius: 8px; text-align: center; font-weight: 600; font-size: 15px;'>Review & Approve Access</a>
                                                                </td>
                                                            </tr>
                                                        </table>

                                                        <p style='margin: 0; font-size: 14px; color: #6b7280; text-align: center;'>
                                                            Access can be managed in the <strong>Incoming Acceptance</strong> tab.
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                            
                                            <!-- External Footer -->
                                            <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 500px; margin-top: 20px;'>
                                                <tr>
                                                    <td align='center' style='padding: 0 20px;'>
                                                        <p style='margin: 0 0 8px 0; font-size: 13px; color: #6b7280; text-align: center;'>
                                                            &copy; 2026 SAPSRI System. All rights reserved. | Automated notification, do not reply.
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                            
                                        </td>
                                    </tr>
                                </table>
                            ";
                            
                            $mail->send();
                        }
                    } catch (Exception $e) {
                        // We catch the error but don't echo it to the user.
                        // The user successfully verified, so we let them through even if the admin email failed.
                        error_log("Admin Notification Mailer Error: {$mail->ErrorInfo}");
                    }

                    // Return success to the frontend
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