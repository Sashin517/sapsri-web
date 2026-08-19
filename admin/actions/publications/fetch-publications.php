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

    // Search filter by Title OR Category Name
    if ($search !== '') {
        $whereClauses[] = "(p.title LIKE ? OR c.name LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
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
    $countQuery = "
        SELECT COUNT(*) as total 
        FROM publications p 
        LEFT JOIN publication_categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.created_by = u.id" . $whereSql;

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
        SELECT p.*, c.name AS category_name, u.first_name, u.last_name 
        FROM publications p 
        LEFT JOIN publication_categories c ON p.category_id = c.id
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
    $pub_result = $stmt->get_result();

    $pub_data = [];
    if ($pub_result && $pub_result->num_rows > 0) {
        while ($row = $pub_result->fetch_assoc()) {
            $pub_id = intval($row['id']);

            $pub_data[] = [
                'id'               => $pub_id,
                'title'            => $row['title'] ?? '',
                'description'      => $row['description'] ?? '',
                'category'         => $row['category_name'] ?? 'Uncategorized',
                'cover_image'      => $row['cover_image'] ? '/project-sedna/' . $row['cover_image'] : '',
                'is_custom_cover'  => (bool)($row['is_custom_cover'] ?? false),
                'file_url'         => $row['file_url'] ? '/project-sedna/' . $row['file_url'] : '',
                'status'           => $row['status'] ?? '',
                'publish_date'     => $row['publish_date'] ?? '',
                'created_at'       => $row['created_at'] ?? '',
                'uploaded_by_name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
            ];
        }
    }
    $stmt->close();

    $pub_data = utf8_clean($pub_data);

    $response = [
        'publications' => $pub_data,
        'pagination'   => [
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
        'error'   => 'Server error',
        'message' => $e->getMessage()
    ]);
}
?>