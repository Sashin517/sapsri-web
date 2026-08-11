<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// 7 items per page as requested
$limit = 7; 
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

try {
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    // 1. Get total count across all three tables to calculate pagination
    $countQuery = "
        SELECT SUM(cnt) as total_count FROM (
            SELECT COUNT(*) as cnt FROM projects
            UNION ALL
            SELECT COUNT(*) as cnt FROM posts
            UNION ALL
            SELECT COUNT(*) as cnt FROM publications
        ) as total_tables
    ";
    $countResult = Database::search($countQuery);
    $totalRecords = 0;
    if ($countResult && mysqli_num_rows($countResult) > 0) {
        $totalRow = mysqli_fetch_assoc($countResult);
        $totalRecords = (int)$totalRow['total_count'];
    }
    $totalPages = ceil($totalRecords / $limit);

    // 2. Fetch the actual paginated data using UNION ALL
    $dataQuery = "
        SELECT t.title, t.type, t.status, u.first_name as lead_name, t.created_at
        FROM (
            SELECT title, 'Project' AS type, status, created_by, created_at FROM projects
            UNION ALL
            SELECT title, 'News Post' AS type, status, created_by, created_at FROM posts
            UNION ALL
            SELECT title, 'Publication' AS type, status, created_by, created_at FROM publications
        ) AS t
        LEFT JOIN users u ON t.created_by = u.id
        ORDER BY t.created_at DESC
        LIMIT {$limit} OFFSET {$offset}
    ";

    $dataResult = Database::search($dataQuery);
    $data = [];
    
    if ($dataResult && mysqli_num_rows($dataResult) > 0) {
        while ($row = mysqli_fetch_assoc($dataResult)) {
            $data[] = [
                'title' => $row['title'],
                'type' => $row['type'],
                'status' => $row['status'],
                'lead' => $row['lead_name'] ?? 'System',
                'created_date' => date('m/d/Y', strtotime($row['created_at']))
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>