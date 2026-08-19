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
    include_once '../../../includes/connection.php';
    
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    $pub_data = [];

    // Join with categories to get the category name directly
    $pub_result = Database::search("
        SELECT p.*, c.name AS category_name, u.first_name, u.last_name 
        FROM publications p 
        LEFT JOIN publication_categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.created_by = u.id
        ORDER BY p.created_at DESC
    ");

    if ($pub_result === false) {
        throw new Exception('Database query failed');
    }

    if (mysqli_num_rows($pub_result) > 0) {
        while ($row = mysqli_fetch_assoc($pub_result)) {
            $pub_id = intval($row['id']);

            $pub_data[] = [
                'id' => $pub_id,
                'title' => $row['title'] ?? '',
                'description' => $row['description'] ?? '',
                'category' => $row['category_name'] ?? 'Uncategorized',
                'cover_image' => $row['cover_image'] ?? '',
                'is_custom_cover' => (bool)$row['is_custom_cover'],
                'file_url' => $row['file_url'] ?? '',
                'status' => $row['status'] ?? '',
                'publish_date' => $row['publish_date'] ?? '',
                // Add this line below to format the full name:
                'uploaded_by_name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
            ];
        }
    }

    $pub_data = utf8_clean($pub_data);

    $json = json_encode(
        (object)$pub_data, 
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );
    
    if ($json === false) {
        $json = json_encode((object)$pub_data);
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