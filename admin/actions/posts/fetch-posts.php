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
    $conn = Database::$connection;
    if ($conn) {
        $conn->set_charset("utf8mb4");
    }

    // Input parameters
    $search    = isset($_GET['search']) ? trim($_GET['search']) : '';
    $filter    = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';
    $dateRange = isset($_GET['date_range']) ? trim($_GET['date_range']) : 'all_time';
    $startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
    $endDate   = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
    $page      = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit     = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
    $offset    = ($page - 1) * $limit;

    $whereClauses = [];
    $params = [];
    $types = '';

    // Search filter by title
    if ($search !== '') {
        $whereClauses[] = "p.title LIKE ?";
        $params[] = '%' . $search . '%';
        $types .= 's';
    }

    // Status filter
    $filterLower = strtolower($filter);
    if ($filterLower === 'published' || $filterLower === 'draft' || $filterLower === 'archived') {
        $whereClauses[] = "LOWER(p.status) = ?";
        $params[] = $filterLower;
        $types .= 's';
    }

    // Date range filter
    $today = date('Y-m-d');
    if ($dateRange === 'today') {
        $whereClauses[] = "DATE(p.created_at) = ?";
        $params[] = $today;
        $types .= 's';
    } elseif ($dateRange === 'yesterday') {
        $whereClauses[] = "DATE(p.created_at) = ?";
        $params[] = date('Y-m-d', strtotime('-1 day'));
        $types .= 's';
    } elseif ($dateRange === 'last_week') {
        $whereClauses[] = "p.created_at >= ?";
        $params[] = date('Y-m-d H:i:s', strtotime('-1 week'));
        $types .= 's';
    } elseif ($dateRange === 'last_month') {
        $whereClauses[] = "p.created_at >= ?";
        $params[] = date('Y-m-d H:i:s', strtotime('-1 month'));
        $types .= 's';
    } elseif ($dateRange === 'last_year') {
        $whereClauses[] = "p.created_at >= ?";
        $params[] = date('Y-m-d H:i:s', strtotime('-1 year'));
        $types .= 's';
    } elseif ($dateRange === 'custom') {
        if ($startDate !== '') {
            $whereClauses[] = "DATE(p.created_at) >= ?";
            $params[] = $startDate;
            $types .= 's';
        }
        if ($endDate !== '') {
            $whereClauses[] = "DATE(p.created_at) <= ?";
            $params[] = $endDate;
            $types .= 's';
        }
    }

    $whereSql = !empty($whereClauses) ? ' WHERE ' . implode(' AND ', $whereClauses) : '';

    // 1. Get total record count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM posts p LEFT JOIN users u ON p.created_by = u.id" . $whereSql;
    $countStmt = $conn->prepare($countQuery);
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
    $countStmt->close();

    $totalPages = ceil($totalRecords / $limit);

    // 2. Fetch records for current page
    $query = "
        SELECT p.id, p.title, p.cover_image, p.content, p.status, p.publish_date,
               p.created_at, p.updated_at, u.first_name, u.last_name
        FROM posts p
        LEFT JOIN users u ON p.created_by = u.id
        {$whereSql}
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ";

    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $post_result = $stmt->get_result();

    $posts_data = [];
    if ($post_result && $post_result->num_rows > 0) {
        while ($row = $post_result->fetch_assoc()) {
            $post_id = intval($row['id']);
            $post_item = [
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

            // Impact Areas
            $impact_res = Database::search("
                SELECT ia.name
                FROM post_impact_areas pia
                JOIN impact_areas ia ON pia.impact_area_id = ia.id
                WHERE pia.post_id = " . $post_id
            );
            if ($impact_res && mysqli_num_rows($impact_res) > 0) {
                while ($impact_row = mysqli_fetch_assoc($impact_res)) {
                    $post_item['impact_areas'][] = $impact_row['name'] ?? '';
                }
            }

            // Post Media
            $media_res = Database::search("
                SELECT media_type, media_url, thumbnail_url
                FROM post_media
                WHERE post_id = " . $post_id
            );
            if ($media_res && mysqli_num_rows($media_res) > 0) {
                while ($media_row = mysqli_fetch_assoc($media_res)) {
                    $post_item['post_media'][] = [
                        'type'          => $media_row['media_type'] ?? '',
                        'url'           => $media_row['media_url'] ?? '',
                        'thumbnail_url' => $media_row['thumbnail_url'] ?? ''
                    ];
                }
            }

            $posts_data[] = $post_item;
        }
    }
    $stmt->close();

    $posts_data = utf8_clean($posts_data);

    $response = [
        'posts' => $posts_data,
        'pagination' => [
            'total_records' => $totalRecords,
            'total_pages'   => max(1, $totalPages),
            'current_page'  => $page,
            'limit'         => $limit
        ]
    ];

    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}
?>