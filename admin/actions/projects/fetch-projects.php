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
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    $projects_data = [];

    // Assuming a standard projects table joined with users for the creator/lead
    $query = "
        SELECT p.id, p.title, p.full_description AS description, p.status, p.project_phase, p.created_at, u.first_name, u.last_name 
        FROM projects p
        LEFT JOIN users u ON p.created_by = u.id
        ORDER BY p.created_at DESC
    ";

    $project_result = Database::search($query);

    if ($project_result === false) {
        throw new Exception('Database query failed');
    }

    if (mysqli_num_rows($project_result) > 0) {
        while ($row = mysqli_fetch_assoc($project_result)) {
            $project_id = intval($row['id']);

            $projects_data[$project_id] = [
                'id' => $project_id,
                'title' => $row['title'] ?? '',
                'description' => $row['description'] ?? '',
                'status' => $row['status'] ?? '',
                'project_phase' => $row['project_phase'] ?? '',
                'created_date' => $row['created_at'] ?? '',
                'project_lead' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))
            ];
        }
    }

    $projects_data = utf8_clean($projects_data);

    $json = json_encode(
        (object)$projects_data, 
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );
    
    if ($json === false) {
        $json = json_encode((object)$projects_data);
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