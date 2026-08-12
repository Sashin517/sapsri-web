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

try {
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    $range = $_GET['range'] ?? 'today';
    $customStart = $_GET['start'] ?? null;
    $customEnd = $_GET['end'] ?? null;

    $now = new DateTime();
    $currentStart = clone $now;
    $currentEnd = clone $now;
    $prevStart = clone $now;
    $prevEnd = clone $now;
    $periodText = "vs yesterday";

    $isAllTime = false;

    // 1. Date Math Logic
    switch ($range) {
        case 'all-time':
            $isAllTime = true;
            $periodText = "Overall Total";
            $cStart = null;
            $cEnd = null;
            $pStart = null;
            $pEnd = null;
            break;
        case 'week':
            $currentStart->modify('-7 days');
            $prevEnd = clone $currentStart;
            $prevStart->modify('-14 days');
            $periodText = "vs last week";
            break;
        case 'month':
            $currentStart->modify('first day of this month');
            $prevEnd = (clone $currentStart)->modify('-1 day');
            $prevStart = (clone $prevEnd)->modify('first day of this month');
            $periodText = "vs last month";
            break;
        case 'year':
            $currentStart->modify('first day of January this year');
            $prevEnd = (clone $currentStart)->modify('-1 day');
            $prevStart = (clone $prevEnd)->modify('first day of January last year');
            $periodText = "vs last year";
            break;
        case 'custom':
            if ($customStart && $customEnd) {
                $currentStart = new DateTime($customStart);
                $currentEnd = new DateTime($customEnd);
                $diff = $currentStart->diff($currentEnd)->days + 1;

                $prevEnd = clone $currentStart;
                $prevEnd->modify('-1 day');
                $prevStart = clone $prevEnd;
                $prevStart->modify("-{$diff} days");
                $periodText = "vs previous {$diff} days";
            }
            break;
        case 'yesterday':
            $currentStart->modify('-1 day');
            $currentEnd->modify('-1 day');
            $prevStart->modify('-2 days');
            $prevEnd->modify('-2 days');
            $periodText = "vs previous day";
            break;
        case 'today':
        default:
            $prevStart->modify('-1 day');
            $prevEnd->modify('-1 day');
            $periodText = "vs yesterday";
            break;
    }
    // Formatting for SQL (only if not 'all-the-time')
    $cStart = $cEnd = $pStart = $pEnd = null;
    if (!$isAllTime) {
        $cStart = $currentStart->format('Y-m-d 00:00:00');
        $cEnd = $currentEnd->format('Y-m-d 23:59:59');
        $pStart = $prevStart->format('Y-m-d 00:00:00');
        $pEnd = $prevEnd->format('Y-m-d 23:59:59');
    }

    // FIX 2: Re-written to use your custom Database::search() method safely
    function getStat($table, $dateCol, $start, $end)
    {

        if ($start !== null && $end !== null) {
            $query = "SELECT COUNT(*) as count FROM {$table} WHERE {$dateCol} BETWEEN '{$start}' AND '{$end}'";
        } else {
            // All time query (no date constraint)
            $query = "SELECT COUNT(*) as count FROM {$table}";
        }

        $result = Database::search($query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return (int)$row['count'];
        }
        return 0; // Return 0 if query fails or table is empty
    }

    // 3. Helper function to calculate trend
    function calculateTrend($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100);
    }

    // 4. Fetching Data
    $stats = [
        'projects' => [
            'current' => getStat('projects', 'created_at', $cStart, $cEnd),
            'previous' => getStat('projects', 'created_at', $pStart, $pEnd)
        ],
        'publications' => [
            'current' => getStat('publications', 'created_at', $cStart, $cEnd),
            'previous' => getStat('publications', 'created_at', $pStart, $pEnd)
        ],
        'users' => [
            'current' => getStat('users', 'created_at', $cStart, $cEnd),
            'previous' => getStat('users', 'created_at', $pStart, $pEnd)
        ],
        'posts' => [
            // FIXED: Table is 'posts' and column is 'created_at'
            'current' => getStat('posts', 'created_at', $cStart, $cEnd),
            'previous' => getStat('posts', 'created_at', $pStart, $pEnd)
        ]
    ];

    // 5. Build Final Payload
    $response = ['status' => 'success', 'period_text' => $periodText, 'data' => []];

    foreach ($stats as $key => $data) {
        $trend = calculateTrend($data['current'], $data['previous']);
        $response['data'][$key] = [
            'count' => $data['current'],
            'trend' => $trend > 0 ? "+{$trend}%" : "{$trend}%",
            'trend_class' => $trend > 0 ? 'text-success bg-success-subtle' : ($trend < 0 ? 'text-danger bg-danger-subtle' : 'text-secondary bg-secondary-subtle')
        ];
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
