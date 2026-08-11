<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

try {
    Database::setUpConnection();
    
    // 1. Fetch Roles
    $roles_query = Database::search("SELECT id, name, description FROM roles ORDER BY id ASC");
    $roles = [];
    while ($r = mysqli_fetch_assoc($roles_query)) {
        $roles[] = $r;
    }

    // 2. Fetch Active & Suspended Users (Excluding Pending)
    $active_users_query = Database::search("
        SELECT u.id, u.first_name, u.last_name, u.email, u.status, r.name as role_name 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.id 
        WHERE u.status IN ('active', 'suspended')
        ORDER BY u.created_at DESC
    ");
    $active_users = [];
    while ($u = mysqli_fetch_assoc($active_users_query)) {
        $active_users[] = $u;
    }

    // 3. Fetch Pending Approvals
    $pending_users_query = Database::search("
        SELECT id, first_name, last_name, email, created_at 
        FROM users 
        WHERE status = 'pending_approval' 
        ORDER BY created_at ASC
    ");
    $pending_users = [];
    while ($p = mysqli_fetch_assoc($pending_users_query)) {
        $pending_users[] = $p;
    }

    echo json_encode([
        'success' => true,
        'roles' => $roles,
        'active_users' => $active_users,
        'pending_users' => $pending_users
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>