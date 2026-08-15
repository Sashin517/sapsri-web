<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

function utf8_clean($data)
{
    if (is_array($data)) {
        return array_map('utf8_clean', $data);
    }
    if (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}

try {
    if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing post ID']);
        exit;
    }

    $post_id = (int)$_GET['id'];

    include_once '../../../includes/connection.php';

    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    // Cleaned query with implicit/concise aliases
    $query = "
        SELECT 
            p.id post_id,
            p.title,
            p.cover_image,
            p.content,
            p.status,
            p.publish_date,
            p.created_at,
            p.updated_at,
            u.first_name,
            u.last_name,
            GROUP_CONCAT(DISTINCT ia.id SEPARATOR '|||') impact_area_ids,
            GROUP_CONCAT(DISTINCT ia.name SEPARATOR '|||') impact_area_names,
            GROUP_CONCAT(
                DISTINCT CONCAT_WS(':::', 
                    IFNULL(pm.id, ''), 
                    IFNULL(pm.media_type, ''), 
                    IFNULL(pm.media_url, ''), 
                    IFNULL(pm.thumbnail_url, '')
                ) SEPARATOR '|||'
            ) media_items
        FROM posts p
        LEFT JOIN users u ON p.created_by = u.id
        LEFT JOIN post_impact_areas pia ON p.id = pia.post_id
        LEFT JOIN impact_areas ia ON pia.impact_area_id = ia.id
        LEFT JOIN post_media pm ON p.id = pm.post_id
        WHERE p.id = $post_id
        GROUP BY p.id
    ";

    $result = Database::search($query);

    if ($result === false) {
        throw new Exception('Database query failed execution');
    }

    if (mysqli_num_rows($result) === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Post not found']);
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    // Parse Impact Areas string back into a clean array
    $impact_area_ids = [];
    if (!empty($row['impact_area_ids'])) {
        $impact_area_ids = explode('|||', $row['impact_area_ids']);
    }

    $impact_area_names = [];
    if (!empty($row['impact_area_names'])) {
        $impact_area_names = explode('|||', $row['impact_area_names']);
    }

    // Parse Media string back into structured array objects
    $post_media = [];
    if (!empty($row['media_items']) && $row['media_items'] !== ':::::::::') {
        $media_entries = explode('|||', $row['media_items']);
        foreach ($media_entries as $entry) {
            $parts = explode(':::', $entry);
            if (count($parts) === 4) {
                $post_media[] = [
                    'id'          => $parts[0],
                    'type'          => $parts[1],
                    'url'           => $parts[2],
                    'thumbnail_url' => $parts[3]
                ];
            }
        }
    }

    // Construct structured response payload
    $post_data = [
        'id'             => (int)$row['post_id'],
        'title'          => $row['title'] ?? '',
        'content'        => $row['content'] ?? '',
        'cover_image'    => $row['cover_image'] ?? '',
        'status'         => $row['status'] ?? '',
        'published_date' => $row['publish_date'] ?? '',
        'created_at'     => $row['created_at'] ?? '',
        'updated_at'     => $row['updated_at'] ?? '',
        'post_lead'      => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
        'impact_area_ids'   => $impact_area_ids,
        'impact_area_names' => $impact_area_names,
        'post_media'     => $post_media
    ];

    $post_data = utf8_clean($post_data);

    echo json_encode(
        $post_data,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Server error',
        'details' => $e->getMessage()
    ]);
}
