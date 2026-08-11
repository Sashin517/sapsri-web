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

    $project_result = Database::search("SELECT * FROM project");

    if ($project_result === false) {
        throw new Exception('Database query failed');
    }

    if (mysqli_num_rows($project_result) > 0) {
        while ($row = mysqli_fetch_assoc($project_result)) {

            $project_id = intval($row['id']);

            $projects_data[$project_id] = [
                'id'          => $project_id,
                'name'        => $row['project_name'] ?? '',
                'description' => $row['project_description'] ?? '',
                'copy_info'   => $row['project_copy'] ?? '',
                'start_date'  => $row['project_start_date'] ?? '',
                'end_date'    => $row['project_end_date'] ?? '',
                'entry_date'  => $row['project_entry_date'] ?? '',
                'status_id'   => isset($row['project_status_id']) ? (int)$row['project_status_id'] : 0,
                'impact_areas' => [],
                'project_media' => [],
                'entities'    => [],
                'locations'   => []
            ];

            // IMPACT AREAS
            $impact_res = Database::search("SELECT impact_area_id FROM project_has_impact_area WHERE project_id = " . $project_id);
            if ($impact_res && mysqli_num_rows($impact_res) > 0) {
                while ($impact_row = mysqli_fetch_assoc($impact_res)) {
                    $impact_id = intval($impact_row['impact_area_id']);
                    $impact_name_res = Database::search("SELECT name FROM impact_area WHERE id = " . $impact_id);
                    if ($impact_name_res && mysqli_num_rows($impact_name_res) > 0) {
                        $impact_name = mysqli_fetch_assoc($impact_name_res);
                        $projects_data[$project_id]['impact_areas'][] = $impact_name['name'] ?? '';
                    }
                }
            }

            // MEDIA
            $media_res = Database::search("SELECT * FROM project_media WHERE project_id = " . $project_id);
            if ($media_res && mysqli_num_rows($media_res) > 0) {
                while ($media_row = mysqli_fetch_assoc($media_res)) {
                    $type_id = intval($media_row['project_media_type_id']);
                    $type_res = Database::search("SELECT project_media_type_name FROM project_media_type WHERE project_media_type_id = " . $type_id);
                    $type_name = '';
                    if ($type_res && mysqli_num_rows($type_res) > 0) {
                        $type_row = mysqli_fetch_assoc($type_res);
                        $type_name = $type_row['project_media_type_name'] ?? '';
                    }
                    $projects_data[$project_id]['project_media'][] = [
                        'type' => $type_name,
                        'url'  => $media_row['project_media_url'] ?? ''
                    ];
                }
            }

            // ENTITIES
            $entity_res = Database::search("SELECT project_entity_id FROM project_has_entity WHERE project_id = " . $project_id);
            if ($entity_res && mysqli_num_rows($entity_res) > 0) {
                while ($entity_row = mysqli_fetch_assoc($entity_res)) {
                    $entity_id = intval($entity_row['project_entity_id']);
                    $entity_detail_res = Database::search("SELECT * FROM project_entity WHERE project_entity_id = " . $entity_id);
                    if ($entity_detail_res && mysqli_num_rows($entity_detail_res) > 0) {
                        $entity_detail = mysqli_fetch_assoc($entity_detail_res);

                        $entity_type_id = intval($entity_detail['project_entity_type_id']);
                        $entity_type_name = '';
                        $entity_type_res = Database::search("SELECT project_entity_type_name FROM project_entity_type WHERE project_entity_type_id = " . $entity_type_id);
                        if ($entity_type_res && mysqli_num_rows($entity_type_res) > 0) {
                            $entity_type_row = mysqli_fetch_assoc($entity_type_res);
                            $entity_type_name = $entity_type_row['project_entity_type_name'] ?? '';
                        }

                        $projects_data[$project_id]['entities'][] = [
                            'name'  => $entity_detail['project_entity_name'] ?? '',
                            'title' => $entity_detail['project_entity_title'] ?? '',
                            'type'  => $entity_type_name
                        ];
                    }
                }
            }

            // LOCATIONS
            $location_res = Database::search("SELECT * FROM project_location WHERE project_id = " . $project_id);
            if ($location_res && mysqli_num_rows($location_res) > 0) {
                while ($location_row = mysqli_fetch_assoc($location_res)) {

                    $district_id = intval($location_row['project_location_district_id']);
                    $district_name = '';
                    $district_res = Database::search("SELECT project_location_district_name FROM project_location_district WHERE project_location_district_id = " . $district_id);
                    if ($district_res && mysqli_num_rows($district_res) > 0) {
                        $district_row = mysqli_fetch_assoc($district_res);
                        $district_name = $district_row['project_location_district_name'] ?? '';
                    }

                    $location_type_id = intval($location_row['project_location_type_id']);
                    $location_type_name = '';
                    $location_type_res = Database::search("SELECT project_location_type_name FROM project_location_type WHERE project_location_type_id = " . $location_type_id);
                    if ($location_type_res && mysqli_num_rows($location_type_res) > 0) {
                        $location_type_row = mysqli_fetch_assoc($location_type_res);
                        $location_type_name = $location_type_row['project_location_type_name'] ?? '';
                    }

                    $projects_data[$project_id]['locations'][] = [
                        'name'      => $location_row['project_location_name'] ?? '',
                        'address'   => $location_row['project_location_address'] ?? '',
                        'latitude'  => $location_row['project_location_latitude'] ?? '',
                        'longitude' => $location_row['project_location_longitude'] ?? '',
                        'district'  => $district_name,
                        'type'      => $location_type_name
                    ];
                }
            }
        }
    }

    // Clean UTF-8 issues
    $projects_data = utf8_clean($projects_data);

    // Encode with UTF-8 safety flags
    $json = json_encode(
        (object)$projects_data,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );

    if ($json === false) {
        // Fallback: try with simpler encoding
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