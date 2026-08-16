<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

// Function to clean UTF-8
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
    // Validate GET parameter
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Publication ID is required']);
        exit;
    }

    $pub_id = intval($_GET['id']);

    include_once '../../../includes/connection.php';
    
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    // Fetch single record matching ID without category name join
    $pub_result = Database::search("
        SELECT p.*, u.first_name, u.last_name 
        FROM publications p 
        LEFT JOIN users u ON p.created_by = u.id
        WHERE p.id = {$pub_id}
        LIMIT 1
    ");

    if ($pub_result === false) {
        throw new Exception('Database query failed');
    }

    if (mysqli_num_rows($pub_result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Publication not found']);
        exit;
    }

    $row = mysqli_fetch_assoc($pub_result);

    $publication = [
        'id' => intval($row['id']),
        'title' => $row['title'] ?? '',
        'description' => $row['description'] ?? '',
        'category_id' => isset($row['category_id']) ? intval($row['category_id']) : null,
        'cover_image' => $row['cover_image'] ? "/project-sedna/" . $row['cover_image'] : '',
        'is_custom_cover' => (bool)($row['is_custom_cover'] ?? false),
        'file_url' => $row['file_url'] ?? '',
        'status' => $row['status'] ?? '',
        'publish_date' => $row['publish_date'] ?? '',
        'created_by' => isset($row['created_by']) ? intval($row['created_by']) : null,
        'uploaded_by_name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
    ];

    $publication = utf8_clean($publication);

    $json = json_encode(
        $publication, 
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );
    
    if ($json === false) {
        $json = json_encode($publication);
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