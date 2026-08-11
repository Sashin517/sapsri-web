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
    include_once 'connection.php';
    
    // Set charset to UTF-8
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    $posts_data = [];

    $post_result = Database::search("SELECT * FROM post");

    if ($post_result === false) {
        throw new Exception('Database query failed');
    }

    if (mysqli_num_rows($post_result) > 0) {
        while ($row = mysqli_fetch_assoc($post_result)) {

            $post_id = intval($row['post_id']);

            $posts_data[$post_id] = [
                'id'   => $post_id,
                'title' => $row['post_name'] ?? '',
                'content' => $row['post_description'] ?? '',
                'copy_info' => $row['post_copy'] ?? '',
                'published_date' => $row['post_date'] ?? '',
                'type_id' => isset($row['post_type_id']) ? (int)$row['post_type_id'] : 0,
                'status_id' => isset($row['post_status_id']) ? (int)$row['post_status_id'] : 0,
                'impact_areas' => [],
                'post_media' => []
            ];

            // IMPACT AREAS
            $impact_res = Database::search("SELECT impact_area_id FROM post_has_impact_area WHERE post_id = " . $post_id);
            if ($impact_res && mysqli_num_rows($impact_res) > 0) {
                while ($impact_row = mysqli_fetch_assoc($impact_res)) {
                    $impact_id = intval($impact_row['impact_area_id']);
                    $impact_name_res = Database::search("SELECT impact_area_name FROM impact_area WHERE impact_area_id = " . $impact_id);
                    if ($impact_name_res && mysqli_num_rows($impact_name_res) > 0) {
                        $impact_name = mysqli_fetch_assoc($impact_name_res);
                        $posts_data[$post_id]['impact_areas'][] = $impact_name['impact_area_name'] ?? '';
                    }
                }
            }

            // MEDIA
            $media_res = Database::search("SELECT * FROM post_media WHERE post_id = " . $post_id);
            if ($media_res && mysqli_num_rows($media_res) > 0) {
                while ($media_row = mysqli_fetch_assoc($media_res)) {
                    $type_id = intval($media_row['post_media_type_id']);
                    $type_res = Database::search("SELECT post_media_type_name FROM post_media_type WHERE post_media_type_id = " . $type_id);
                    if ($type_res && mysqli_num_rows($type_res) > 0) {
                        $type_row = mysqli_fetch_assoc($type_res);
                        $posts_data[$post_id]['post_media'][] = [
                            'type' => $type_row['post_media_type_name'] ?? '',
                            'url' => $media_row['post_media_url'] ?? ''
                        ];
                    }
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
        // Fallback: try with simpler encoding
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