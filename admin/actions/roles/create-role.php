<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

try {
    Database::setUpConnection();
    $conn = Database::$connection;
    
    // Start Transaction to ensure data integrity across both tables
    mysqli_begin_transaction($conn);

    $role_name = trim($_POST['role_name'] ?? '');
    $role_desc = trim($_POST['role_description'] ?? '');
    $permissions = $_POST['permissions'] ?? [];

    if (empty($role_name)) {
        throw new Exception("Role name is required.");
    }

    // 1. Insert Core Role
    $stmt = $conn->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $role_name, $role_desc);
    $stmt->execute();
    $role_id = $conn->insert_id;
    $stmt->close();

    // 2. Map and Insert Permissions Matrix
    $modules = ['dashboard', 'projects', 'posts', 'publications', 'users'];
    
    $perm_stmt = $conn->prepare("INSERT INTO role_permissions (role_id, module_name, can_view, can_create, can_edit, can_delete, can_publish) VALUES (?, ?, ?, ?, ?, ?, ?)");

    foreach ($modules as $module) {
        $view    = isset($permissions[$module]['view']) ? 1 : 0;
        $create  = isset($permissions[$module]['create']) ? 1 : 0;
        $edit    = isset($permissions[$module]['edit']) ? 1 : 0;
        $delete  = isset($permissions[$module]['delete']) ? 1 : 0;
        $publish = isset($permissions[$module]['publish']) ? 1 : 0;

        $perm_stmt->bind_param("isiiiii", $role_id, $module, $view, $create, $edit, $delete, $publish);
        $perm_stmt->execute();
    }
    
    $perm_stmt->close();

    // Commit if everything succeeds
    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Role created successfully!']);

} catch (Exception $e) {
    if (isset($conn)) mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>