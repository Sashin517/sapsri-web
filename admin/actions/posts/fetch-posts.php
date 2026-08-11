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

    // Set charset to UTF-8
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    $posts_data = [];

    // Base post data, with creator name joined in (same pattern as fetch-projects.php)
    $post_result = Database::search("
        SELECT p.id, p.title, p.cover_image, p.content, p.status, p.publish_date,
               p.created_at, p.updated_at, u.first_name, u.last_name
        FROM posts p
        LEFT JOIN users u ON p.created_by = u.id
        ORDER BY p.created_at DESC
    ");

    if ($post_result === false) {
        throw new Exception('Database query failed');
    }

    if (mysqli_num_rows($post_result) > 0) {
        while ($row = mysqli_fetch_assoc($post_result)) {

            $post_id = intval($row['id']);

            $posts_data[$post_id] = [
                'id'             => $post_id,
                'title'          => $row['title'] ?? '',
                'content'        => $row['content'] ?? '',
                'cover_image'    => $row['cover_image'] ?? '',
                'status'         => $row['status'] ?? '',
                'published_date' => $row['publish_date'] ?? '',
                'created_at'     => $row['created_at'] ?? '',
                'post_lead'      => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                'impact_areas'   => [],
                'post_media'     => []
            ];

            // IMPACT AREAS — post_impact_areas joined directly to impact_areas, no separate lookup step needed
            $impact_res = Database::search("
                SELECT ia.name
                FROM post_impact_areas pia
                JOIN impact_areas ia ON pia.impact_area_id = ia.id
                WHERE pia.post_id = " . $post_id
            );
            if ($impact_res && mysqli_num_rows($impact_res) > 0) {
                while ($impact_row = mysqli_fetch_assoc($impact_res)) {
                    $posts_data[$post_id]['impact_areas'][] = $impact_row['name'] ?? '';
                }
            }

            // MEDIA — media_type is already a plain enum column on post_media, no lookup table needed
            $media_res = Database::search("
                SELECT media_type, media_url, thumbnail_url
                FROM post_media
                WHERE post_id = " . $post_id
            );
            if ($media_res && mysqli_num_rows($media_res) > 0) {
                while ($media_row = mysqli_fetch_assoc($media_res)) {
                    $posts_data[$post_id]['post_media'][] = [
                        'type'          => $media_row['media_type'] ?? '',
                        'url'           => $media_row['media_url'] ?? '',
                        'thumbnail_url' => $media_row['thumbnail_url'] ?? ''
                    ];
                }
            }
        }
    }

    // Clean UTF-8 issues
    $posts_data = utf8_clean($posts_data);

    // Encode with UTF-8 safety flags
    $json = json_encode(
        (object)$posts_data,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );

    if ($json === false) {
        $json = json_encode((object)$posts_data);
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