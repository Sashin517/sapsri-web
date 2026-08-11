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

    $projects_data = [];

    // Main Query: Fetch only 'published' projects and join user for creator name
    $query = "
        SELECT p.id, p.title, p.cover_image, p.full_description, p.project_phase, 
               p.start_date, p.end_date, p.created_at, u.first_name, u.last_name 
        FROM projects p
        LEFT JOIN users u ON p.created_by = u.id
        WHERE p.status = 'published'
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
                'id'               => $project_id,
                'title'            => $row['title'] ?? '',
                'cover_image'      => $row['cover_image'] ?? '',
                'description'      => $row['full_description'] ?? '',
                'phase'            => $row['project_phase'] ?? '',
                'start_date'       => $row['start_date'] ?? '',
                'end_date'         => $row['end_date'] ?? '',
                'created_at'       => $row['created_at'] ?? '',
                'creator_name'     => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                'impact_areas'     => [],
                'metrics'          => [],
                'success_stories'  => [],
                'leads'            => [],
                'media'            => []
            ];

            // 1. IMPACT AREAS
            $impact_res = Database::search("
                SELECT ia.name 
                FROM project_impact_areas pia 
                JOIN impact_areas ia ON pia.impact_area_id = ia.id 
                WHERE pia.project_id = " . $project_id
            );
            if ($impact_res && mysqli_num_rows($impact_res) > 0) {
                while ($impact_row = mysqli_fetch_assoc($impact_res)) {
                    $projects_data[$project_id]['impact_areas'][] = $impact_row['name'] ?? '';
                }
            }

            // 2. METRICS
            $metrics_res = Database::search("SELECT * FROM project_metrics WHERE project_id = " . $project_id . " ORDER BY section_number ASC");
            if ($metrics_res && mysqli_num_rows($metrics_res) > 0) {
                while ($metric_row = mysqli_fetch_assoc($metrics_res)) {
                    $projects_data[$project_id]['metrics'][] = [
                        'section'       => $metric_row['section_number'] ?? '',
                        'section_image' => $metric_row['section_image'] ?? '',
                        'icon_image'    => $metric_row['icon_image'] ?? '',
                        'value'         => $metric_row['metric_value'] ?? '',
                        'label'         => $metric_row['metric_label'] ?? ''
                    ];
                }
            }

            // 3. SUCCESS STORIES
            $stories_res = Database::search("SELECT * FROM project_success_stories WHERE project_id = " . $project_id);
            if ($stories_res && mysqli_num_rows($stories_res) > 0) {
                while ($story_row = mysqli_fetch_assoc($stories_res)) {
                    $projects_data[$project_id]['success_stories'][] = [
                        'name'        => $story_row['subject_name'] ?? '',
                        'image'       => $story_row['subject_image'] ?? '',
                        'description' => $story_row['subject_description'] ?? ''
                    ];
                }
            }

            // 4. PROJECT LEADS
            $leads_res = Database::search("SELECT * FROM project_leads WHERE project_id = " . $project_id);
            if ($leads_res && mysqli_num_rows($leads_res) > 0) {
                while ($lead_row = mysqli_fetch_assoc($leads_res)) {
                    $projects_data[$project_id]['leads'][] = [
                        'name'     => $lead_row['name'] ?? '',
                        'role'     => $lead_row['role_designation'] ?? '',
                        'photo'    => $lead_row['profile_photo'] ?? '',
                        'linkedin' => $lead_row['linkedin_profile'] ?? ''
                    ];
                }
            }

            // 5. MEDIA GALLERY
            $media_res = Database::search("SELECT media_type, media_url FROM project_media WHERE project_id = " . $project_id);
            if ($media_res && mysqli_num_rows($media_res) > 0) {
                while ($media_row = mysqli_fetch_assoc($media_res)) {
                    $projects_data[$project_id]['media'][] = [
                        'type' => $media_row['media_type'] ?? '',
                        'url'  => $media_row['media_url'] ?? ''
                    ];
                }
            }
        }
    }

    // Clean UTF-8 issues
    $projects_data = utf8_clean($projects_data);

    // Output as a clean JSON Array
    $json = json_encode(
        array_values($projects_data),
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );

    if ($json === false) {
        $json = json_encode(array_values($projects_data));
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