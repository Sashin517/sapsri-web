<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

try {
    Database::setUpConnection();
    $conn = Database::$connection;

    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $action = $_POST['action'] ?? '';
    $role_id = isset($_POST['role_id']) ? intval($_POST['role_id']) : null;

    if ($user_id === 0 || empty($action)) {
        throw new Exception("Invalid request parameters.");
    }

    if ($action === 'accept') {
        if (!$role_id) throw new Exception("Please select a role to accept the user.");
        
        $stmt = $conn->prepare("UPDATE users SET role_id = ?, status = 'active' WHERE id = ?");
        $stmt->bind_param("ii", $role_id, $user_id);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'User accepted and assigned to role.']);
    
    } elseif ($action === 'reject') {
        // Since 'rejected' is not in the ENUM, we delete the pending request.
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND status = 'pending_approval'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'User request rejected and removed.']);
    } else {
        throw new Exception("Unknown action.");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>