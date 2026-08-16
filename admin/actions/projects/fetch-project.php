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
    // 1. Validate the GET parameter
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid or missing project ID'
        ]);
        exit;
    }

    $project_id = intval($_GET['id']);

    // 2. Include database connection
    include_once '../../../includes/connection.php';
    
    Database::setUpConnection();
    if (Database::$connection) {
        Database::$connection->set_charset("utf8mb4");
    }

    // 3. Fetch Main Project Details
    $project_query = "
        SELECT id, title, cover_image, full_description, status, project_phase, start_date, end_date
        FROM projects
        WHERE id = {$project_id}
        LIMIT 1
    ";

    $project_result = Database::search($project_query);

    if ($project_result === false) {
        throw new Exception('Database query failed for project details');
    }

    if (mysqli_num_rows($project_result) === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Project not found'
        ]);
        exit;
    }

    $project_row = mysqli_fetch_assoc($project_result);

    // 4. Fetch Impact Area IDs
    $impact_area_ids = [];
    $impact_query = "SELECT impact_area_id FROM project_impact_areas WHERE project_id = {$project_id}";
    $impact_result = Database::search($impact_query);

    if ($impact_result && mysqli_num_rows($impact_result) > 0) {
        while ($row = mysqli_fetch_assoc($impact_result)) {
            $impact_area_ids[] = intval($row['impact_area_id']);
        }
    }

    // 5. Fetch Project Metrics
    $project_metrics = [];
    $metrics_query = "
        SELECT id, section_number, section_image, icon_image, metric_value, metric_label 
        FROM project_metrics 
        WHERE project_id = {$project_id}
    ";
    $metrics_result = Database::search($metrics_query);

    if ($metrics_result && mysqli_num_rows($metrics_result) > 0) {
        while ($row = mysqli_fetch_assoc($metrics_result)) {
            $project_metrics[] = [
                'id' => intval($row['id']),
                'section_number' => $row['section_number'] ?? '',
                'section_image' => "/project-sedna/" . $row['section_image'] ?? '',
                'icon_image' => "/project-sedna/" . $row['icon_image'] ?? '',
                'metric_value' => $row['metric_value'] ?? '',
                'metric_label' => $row['metric_label'] ?? ''
            ];
        }
    }

    // 6. Fetch Success Stories
    $project_success_stories = [];
    $stories_query = "
        SELECT id, subject_name, subject_image, subject_description 
        FROM project_success_stories 
        WHERE project_id = {$project_id}
    ";
    $stories_result = Database::search($stories_query);

    if ($stories_result && mysqli_num_rows($stories_result) > 0) {
        while ($row = mysqli_fetch_assoc($stories_result)) {
            $project_success_stories[] = [
                'id' => intval($row['id']),
                'subject_name' => $row['subject_name'] ?? '',
                'subject_image' => "/project-sedna/" . $row['subject_image'] ?? '',
                'subject_description' => $row['subject_description'] ?? ''
            ];
        }
    }

    // 7. Fetch Project Leads
    $project_leads = [];
    $leads_query = "
        SELECT id, name, role_designation, profile_photo, linkedin_profile 
        FROM project_leads 
        WHERE project_id = {$project_id}
    ";
    $leads_result = Database::search($leads_query);

    if ($leads_result && mysqli_num_rows($leads_result) > 0) {
        while ($row = mysqli_fetch_assoc($leads_result)) {
            $project_leads[] = [
                'id' => intval($row['id']),
                'name' => $row['name'] ?? '',
                'role_designation' => $row['role_designation'] ?? '',
                'profile_photo' => "/project-sedna/" . $row['profile_photo'] ?? '',
                'linkedin_profile' => $row['linkedin_profile'] ?? ''
            ];
        }
    }

    // 8. Fetch Project Media
    $project_media = [];
    $media_query = "
        SELECT id, media_type, media_url, thumbnail_url, created_at 
        FROM project_media 
        WHERE project_id = {$project_id}
        ORDER BY created_at DESC
    ";
    $media_result = Database::search($media_query);

    if ($media_result && mysqli_num_rows($media_result) > 0) {
        while ($row = mysqli_fetch_assoc($media_result)) {
            $project_media[] = [
                'id' => intval($row['id']),
                'media_type' => $row['media_type'] ?? 'image',
                'media_url' => "/project-sedna/" . $row['media_url'] ?? '',
                'thumbnail_url' => $row['thumbnail_url'] ?? '',
                'created_at' => $row['created_at'] ?? ''
            ];
        }
    }

    // 9. Assemble Full Response Payload
    $response_data = [
        'project' => [
            'id' => intval($project_row['id']),
            'title' => $project_row['title'] ?? '',
            'cover_image' => "/project-sedna/" . $project_row['cover_image'] ?? '',
            'full_description' => $project_row['full_description'] ?? '',
            'status' => $project_row['status'] ?? '',
            'project_phase' => $project_row['project_phase'] ?? '',
            'start_date' => $project_row['start_date'] ?? '',
            'end_date' => $project_row['end_date'] ?? ''
        ],
        'project_impact_areas' => [
            'impact_area_ids' => $impact_area_ids
        ],
        'project_metrics' => $project_metrics,
        'project_success_stories' => $project_success_stories,
        'project_leads' => $project_leads,
        'project_media' => $project_media
    ];

    // 10. Clean UTF-8 strings and output JSON
    $response_data = utf8_clean($response_data);

    $json = json_encode(
        $response_data, 
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );
    
    if ($json === false) {
        $json = json_encode($response_data);
    }

    echo $json;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>