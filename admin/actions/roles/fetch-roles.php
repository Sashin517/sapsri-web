<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

function utf8_clean($data) {
    if (is_array($data)) {
        return array_map('utf8_clean', $data);
    }
    if (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}

try {
    include_once '../../../includes/connection.php';
    
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    $roles_data = [];

    $role_result = Database::search("SELECT * FROM roles ORDER BY id ASC");

    if ($role_result === false) {
        throw new Exception('Database query failed');
    }

    if (mysqli_num_rows($role_result) > 0) {
        while ($row = mysqli_fetch_assoc($role_result)) {
            $role_id = intval($row['id']);

            $roles_data[$role_id] = [
                'id' => $role_id,
                'name' => $row['name'] ?? '',
                'description' => $row['description'] ?? '',
                'permissions' => []
            ];

            // Fetch granular permissions for this specific role
            $perm_res = Database::search("SELECT * FROM role_permissions WHERE role_id = " . $role_id);
            if ($perm_res && mysqli_num_rows($perm_res) > 0) {
                while ($perm_row = mysqli_fetch_assoc($perm_res)) {
                    $module = $perm_row['module_name'];
                    $roles_data[$role_id]['permissions'][$module] = [
                        'can_view' => (bool)$perm_row['can_view'],
                        'can_create' => (bool)$perm_row['can_create'],
                        'can_edit' => (bool)$perm_row['can_edit'],
                        'can_delete' => (bool)$perm_row['can_delete'],
                        'can_publish' => (bool)$perm_row['can_publish']
                    ];
                }
            }
        }
    }

    $roles_data = utf8_clean($roles_data);

    $json = json_encode(
        (object)$roles_data, 
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );
    
    if ($json === false) {
        $json = json_encode((object)$roles_data);
    }

    echo $json;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}
?>