<?php
session_start();
require_once '../../../includes/connection.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    Database::setUpConnection();
    $conn = Database::$connection;

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
        exit;
    }

    // UPDATED SQL: Join the users table (u) with the roles table (r) to fetch the role name
    $stmt = $conn->prepare("
        SELECT u.id, u.first_name, u.last_name, u.role_id, r.name AS role_name, u.password_hash, u.status 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.id 
        WHERE u.email = ?
    ");
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            
            // Check UI states based on ENUM setup
            if ($user['status'] === 'pending_otp') {
                echo json_encode(['status' => 'error', 'message' => 'Please verify your email address to continue.']);
                exit;
            }
            if ($user['status'] === 'pending_approval') {
                echo json_encode(['status' => 'error', 'message' => 'Your account is pending administrator approval.']);
                exit;
            }
            if ($user['status'] === 'suspended') {
                echo json_encode(['status' => 'error', 'message' => 'Your account has been suspended. Contact an administrator.']);
                exit;
            }

            // Success: Set session vars
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_role_id'] = $user['role_id'];
            $_SESSION['admin_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            // NEW: Store the actual role name in the session (fallback to 'User' if null)
            $_SESSION['admin_role_name'] = $user['role_name'] ?? 'User';
            
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Incorrect email or password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect email or password.']);
    }
}
?>