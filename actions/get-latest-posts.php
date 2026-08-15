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
    include_once '../includes/connection.php';

    // Set charset to UTF-8
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    $posts_data = [];

    // Select only required fields, ordered by publish_date, limited to 10 latest posts
    $post_result = Database::search("
        SELECT id, title, cover_image, content, publish_date
        FROM posts
        ORDER BY publish_date DESC
        LIMIT 10
    ");

    if ($post_result === false) {
        throw new Exception('Database query failed');
    }

    if (mysqli_num_rows($post_result) > 0) {
        while ($row = mysqli_fetch_assoc($post_result)) {
            // Strip HTML tags and limit content to ~100 characters
            $clean_content = strip_tags($row['content'] ?? '');
            $short_description = mb_strimwidth($clean_content, 0, 100, '...');

            $posts_data[] = [
                'id'             => intval($row['id']),
                'title'          => $row['title'] ?? '',
                'cover_image'    => $row['cover_image'] ?? '',
                'description'    => $short_description,
                'published_date' => $row['publish_date'] ?? ''
            ];
        }
    }

    // Clean UTF-8 issues
    $posts_data = utf8_clean($posts_data);

    // Encode with UTF-8 safety flags
    $json = json_encode(
        $posts_data,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );

    if ($json === false) {
        $json = json_encode($posts_data);
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