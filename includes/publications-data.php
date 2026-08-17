<?php
header('Content-Type: application/json; charset=utf-8');
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
    // Make sure this path correctly points to your connection file
    include_once 'connection.php';

    // Set charset to UTF-8
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    } else {
        throw new Exception("Failed to establish database connection.");
    }

    $publications_data = [];

    // Main Query: Fetch 'published' publications, join categories, and join user for creator name
    $query = "
        SELECT p.id, p.title, p.description, p.cover_image, p.file_url, p.publish_date, p.created_at, p.updated_at,
               pc.name AS category_name,
               u.first_name, u.last_name 
        FROM publications p
        LEFT JOIN publication_categories pc ON p.category_id = pc.id
        LEFT JOIN users u ON p.created_by = u.id
        WHERE p.status = 'published'
        ORDER BY p.created_at DESC
    ";
    
    $pub_result = Database::search($query);

    if ($pub_result === false) {
        throw new Exception('Database query failed while fetching publications.');
    }

    if (mysqli_num_rows($pub_result) > 0) {
        while ($row = mysqli_fetch_assoc($pub_result)) {

            $pub_id = intval($row['id']);

            $publications_data[$pub_id] = [
                'id'               => $pub_id,
                'title'            => $row['title'] ?? '',
                'description'      => $row['description'] ?? '',
                'category_name'    => $row['category_name'] ?? 'Uncategorized',
                'cover_image'      => $row['cover_image'] ?? '',
                'file_url'         => $row['file_url'] ?? '',
                'publish_date'     => $row['publish_date'] ?? '',
                'created_at'       => $row['created_at'] ?? '',
                'updated_at'       => $row['updated_at'] ?? '',
                'creator_name'     => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
            ];
        }
    }

    // Clean UTF-8 issues
    $publications_data = utf8_clean($publications_data);

    // Output as a clean JSON Array (using array_values to strip the pub_id keys for a standard JSON array list)
    $json = json_encode(
        array_values($publications_data),
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );

    if ($json === false) {
        $json = json_encode(array_values($publications_data));
    }

    echo $json;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Server error',
        'message' => $e->getMessage() 
    ]);
}
?>