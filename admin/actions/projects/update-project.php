<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

// Ensure directories exist
$img_dir = '../../../assets/media/img/projects/';
$vid_dir = '../../../assets/media/videos/projects/';
if (!file_exists($img_dir)) mkdir($img_dir, 0777, true);
if (!file_exists($vid_dir)) mkdir($vid_dir, 0777, true);

// Helper function to handle WebP Conversion and File Uploads
function uploadFile($file, $target_dir, $prefix = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    
    $mime = mime_content_type($file['tmp_name']);
    $is_video = strpos($mime, 'video/') === 0;

    // 1. If video, bypass WebP conversion
    if ($is_video) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . uniqid() . '.' . $ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return str_replace('../../../', '', $target_file);
        }
        return null;
    } 
    
    // 2. If image, convert to WebP
    else {
        $filename = $prefix . uniqid() . '.webp';
        $target_file = $target_dir . $filename;
        $image = null;

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($file['tmp_name']);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($file['tmp_name']);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($file['tmp_name']);
                break;
            default:
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fallback_target = $target_dir . $prefix . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $fallback_target)) {
                    return str_replace('../../../', '', $fallback_target);
                }
                return null;
        }

        if ($image !== false && $image !== null) {
            $success = imagewebp($image, $target_file, 80);
            unset($image);
            
            if ($success) {
                return str_replace('../../../', '', $target_file);
            }
        }
        return null;
    }
}

// Helper to unlink physical server files
function deletePhysicalFile($relative_path) {
    if (empty($relative_path)) return;
    $full_path = '../../../' . ltrim($relative_path, '/');
    if (file_exists($full_path) && is_file($full_path)) {
        @unlink($full_path);
    }
}

try {
    Database::setUpConnection();
    $conn = Database::$connection;
    
    // Begin Database Transaction
    mysqli_begin_transaction($conn);

    $project_id = intval($_POST['project_id'] ?? 0);
    if ($project_id <= 0) {
        throw new Exception("Invalid or missing project ID.");
    }

    $title = $_POST['title'] ?? 'Untitled';
    $phase = $_POST['phase'] ?? 'ongoing';
    $description = $_POST['full_description'] ?? '';
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    
    $impact_areas = $_POST['impact_area'] ?? [];
    if (!is_array($impact_areas) && !empty($impact_areas)) {
        $impact_areas = [$impact_areas];
    }

    if ($phase === 'ongoing') {
        $end_date = null;
    }

    // Decode JS Removal Tracking Object
    $removedItems = json_decode($_POST['removed_items'] ?? '{}', true);

    // -------------------------------------------------------------
    // 1. UPDATE CORE PROJECT DATA & COVER IMAGE
    // -------------------------------------------------------------
    // Fetch existing cover image path
    $stmt = $conn->prepare("SELECT cover_image FROM projects WHERE id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $existing_project = $res->fetch_assoc();
    $stmt->close();

    $cover_path = $existing_project['cover_image'] ?? null;

    // Check if cover image was removed or replaced
    if (!empty($removedItems['coverImageRemoved'])) {
        deletePhysicalFile($cover_path);
        $cover_path = null;
    }

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        // Remove existing file if replaced
        deletePhysicalFile($cover_path);
        $cover_path = uploadFile($_FILES['cover_image'], $img_dir, 'cover_');
    }

    // Dynamic update check for `status`
    if (array_key_exists('status', $_POST)) {
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE projects SET title = ?, cover_image = ?, full_description = ?, status = ?, project_phase = ?, start_date = ?, end_date = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $title, $cover_path, $description, $status, $phase, $start_date, $end_date, $project_id);
    } else {
        $stmt = $conn->prepare("UPDATE projects SET title = ?, cover_image = ?, full_description = ?, project_phase = ?, start_date = ?, end_date = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $title, $cover_path, $description, $phase, $start_date, $end_date, $project_id);
    }
    
    $stmt->execute();
    $stmt->close();

    // -------------------------------------------------------------
    // 2. UPDATE IMPACT AREAS
    // -------------------------------------------------------------
    // Synchronize by deleting all previous links and re-inserting active selections
    $stmt = $conn->prepare("DELETE FROM project_impact_areas WHERE project_id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $stmt->close();

    if (!empty($impact_areas)) {
        $stmt = $conn->prepare("INSERT INTO project_impact_areas (project_id, impact_area_id) VALUES (?, ?)");
        foreach ($impact_areas as $area_id) {
            $area_id_int = intval($area_id);
            if ($area_id_int > 0) {
                $stmt->bind_param("ii", $project_id, $area_id_int);
                $stmt->execute();
            }
        }
        $stmt->close();
    }

    // -------------------------------------------------------------
    // 3. REMOVE DELETED METRICS / ICONS
    // -------------------------------------------------------------
    if (!empty($removedItems['removedMetricIds'])) {
        foreach ($removedItems['removedMetricIds'] as $m_id) {
            $m_id_int = intval($m_id);
            if ($m_id_int > 0) {
                // Delete physical files first
                $stmt = $conn->prepare("SELECT icon_image, section_image FROM project_metrics WHERE id = ? AND project_id = ?");
                $stmt->bind_param("ii", $m_id_int, $project_id);
                $stmt->execute();
                $m_res = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($m_res) {
                    deletePhysicalFile($m_res['icon_image']);
                    deletePhysicalFile($m_res['section_image']);
                }

                $stmt = $conn->prepare("DELETE FROM project_metrics WHERE id = ? AND project_id = ?");
                $stmt->bind_param("ii", $m_id_int, $project_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    if (!empty($removedItems['removedMetricIconIds'])) {
        foreach ($removedItems['removedMetricIconIds'] as $m_id) {
            $m_id_int = intval($m_id);
            if ($m_id_int > 0) {
                $stmt = $conn->prepare("SELECT icon_image FROM project_metrics WHERE id = ? AND project_id = ?");
                $stmt->bind_param("ii", $m_id_int, $project_id);
                $stmt->execute();
                $m_res = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($m_res && !empty($m_res['icon_image'])) {
                    deletePhysicalFile($m_res['icon_image']);
                    $stmt = $conn->prepare("UPDATE project_metrics SET icon_image = NULL WHERE id = ? AND project_id = ?");
                    $stmt->bind_param("ii", $m_id_int, $project_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    // -------------------------------------------------------------
    // 4. PROCESS METRIC SECTIONS (INSERT / UPDATE)
    // -------------------------------------------------------------
    for ($sec = 1; $sec <= 2; $sec++) {
        $sec_img_key = 'sec' . $sec . '_image';
        $sec_img_path = null;

        if (!empty($removedItems["sec{$sec}ImageRemoved"])) {
            // Delete old section images across this section
            $stmt = $conn->prepare("SELECT DISTINCT section_image FROM project_metrics WHERE project_id = ? AND section_number = ? AND section_image IS NOT NULL");
            $sec_str = (string)$sec;
            $stmt->bind_param("is", $project_id, $sec_str);
            $stmt->execute();
            $s_res = $stmt->get_result();
            while ($row = $s_res->fetch_assoc()) {
                deletePhysicalFile($row['section_image']);
            }
            $stmt->close();

            $stmt = $conn->prepare("UPDATE project_metrics SET section_image = NULL WHERE project_id = ? AND section_number = ?");
            $stmt->bind_param("is", $project_id, $sec_str);
            $stmt->execute();
            $stmt->close();
        }

        if (isset($_FILES[$sec_img_key]) && $_FILES[$sec_img_key]['error'] === UPLOAD_ERR_OK) {
            $sec_img_path = uploadFile($_FILES[$sec_img_key], $img_dir, "s{$sec}_");
        }

        if (isset($_POST["sec{$sec}_metrics"])) {
            foreach ($_POST["sec{$sec}_metrics"] as $index => $metricData) {
                $metric_db_id = intval($metricData['id'] ?? 0);
                $val = $metricData['value'] ?? '';
                $lbl = $metricData['label'] ?? '';

                $icon_path = null;
                $file_key = "sec{$sec}_metrics";

                if (isset($_FILES[$file_key]['name'][$index]['icon'])) {
                    $file = [
                        'name' => $_FILES[$file_key]['name'][$index]['icon'],
                        'type' => $_FILES[$file_key]['type'][$index]['icon'],
                        'tmp_name' => $_FILES[$file_key]['tmp_name'][$index]['icon'],
                        'error' => $_FILES[$file_key]['error'][$index]['icon'],
                        'size' => $_FILES[$file_key]['size'][$index]['icon']
                    ];
                    if ($file['error'] === UPLOAD_ERR_OK) {
                        $icon_path = uploadFile($file, $img_dir, "icon_");
                    }
                }

                if ($metric_db_id > 0) {
                    // Update Existing Metric
                    if ($icon_path) {
                        $stmt = $conn->prepare("SELECT icon_image FROM project_metrics WHERE id = ?");
                        $stmt->bind_param("i", $metric_db_id);
                        $stmt->execute();
                        $existing_icon = $stmt->get_result()->fetch_assoc();
                        $stmt->close();
                        deletePhysicalFile($existing_icon['icon_image'] ?? null);

                        $stmt = $conn->prepare("UPDATE project_metrics SET metric_value = ?, metric_label = ?, icon_image = ? WHERE id = ? AND project_id = ?");
                        $stmt->bind_param("sssii", $val, $lbl, $icon_path, $metric_db_id, $project_id);
                    } else {
                        $stmt = $conn->prepare("UPDATE project_metrics SET metric_value = ?, metric_label = ? WHERE id = ? AND project_id = ?");
                        $stmt->bind_param("ssii", $val, $lbl, $metric_db_id, $project_id);
                    }
                    $stmt->execute();
                    $stmt->close();

                    if ($sec_img_path) {
                        $stmt = $conn->prepare("UPDATE project_metrics SET section_image = ? WHERE id = ? AND project_id = ?");
                        $stmt->bind_param("sii", $sec_img_path, $metric_db_id, $project_id);
                        $stmt->execute();
                        $stmt->close();
                    }
                } else {
                    // Insert New Metric
                    $stmt = $conn->prepare("INSERT INTO project_metrics (project_id, section_number, section_image, icon_image, metric_value, metric_label) VALUES (?, ?, ?, ?, ?, ?)");
                    $sec_str = (string)$sec;
                    $stmt->bind_param("isssss", $project_id, $sec_str, $sec_img_path, $icon_path, $val, $lbl);
                    $stmt->execute();
                    $stmt->close();
                }
                
                $sec_img_path = null; // Only apply section image to the first metric row processed
            }
        }
    }

    // -------------------------------------------------------------
    // 5. REMOVE AND UPDATE SUCCESS STORIES
    // -------------------------------------------------------------
    if (!empty($removedItems['removedStoryIds'])) {
        foreach ($removedItems['removedStoryIds'] as $s_id) {
            $s_id_int = intval($s_id);
            if ($s_id_int > 0) {
                $stmt = $conn->prepare("SELECT subject_image FROM project_success_stories WHERE id = ? AND project_id = ?");
                $stmt->bind_param("ii", $s_id_int, $project_id);
                $stmt->execute();
                $s_res = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                deletePhysicalFile($s_res['subject_image'] ?? null);

                $stmt = $conn->prepare("DELETE FROM project_success_stories WHERE id = ? AND project_id = ?");
                $stmt->bind_param("ii", $s_id_int, $project_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    if (!empty($removedItems['removedStoryImageIds'])) {
        foreach ($removedItems['removedStoryImageIds'] as $s_id) {
            $s_id_int = intval($s_id);
            if ($s_id_int > 0) {
                $stmt = $conn->prepare("SELECT subject_image FROM project_success_stories WHERE id = ? AND project_id = ?");
                $stmt->bind_param("ii", $s_id_int, $project_id);
                $stmt->execute();
                $s_res = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                deletePhysicalFile($s_res['subject_image'] ?? null);

                $stmt = $conn->prepare("UPDATE project_success_stories SET subject_image = NULL WHERE id = ? AND project_id = ?");
                $stmt->bind_param("ii", $s_id_int, $project_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    if (isset($_POST['stories'])) {
        foreach ($_POST['stories'] as $index => $story) {
            $story_db_id = intval($story['id'] ?? 0);
            $desc = $story['description'] ?? '';
            $s_name = $story['name'] ?? '';

            $story_img = null;
            if (isset($_FILES['stories']['name'][$index]['image'])) {
                $file = [
                    'name' => $_FILES['stories']['name'][$index]['image'],
                    'type' => $_FILES['stories']['type'][$index]['image'],
                    'tmp_name' => $_FILES['stories']['tmp_name'][$index]['image'],
                    'error' => $_FILES['stories']['error'][$index]['image'],
                    'size' => $_FILES['stories']['size'][$index]['image']
                ];
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $story_img = uploadFile($file, $img_dir, "story_");
                }
            }

            if ($story_db_id > 0) {
                // Update Existing
                if ($story_img) {
                    $stmt = $conn->prepare("SELECT subject_image FROM project_success_stories WHERE id = ?");
                    $stmt->bind_param("i", $story_db_id);
                    $stmt->execute();
                    $existing_story_img = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                    deletePhysicalFile($existing_story_img['subject_image'] ?? null);

                    $stmt = $conn->prepare("UPDATE project_success_stories SET subject_name = ?, subject_description = ?, subject_image = ? WHERE id = ? AND project_id = ?");
                    $stmt->bind_param("sssii", $s_name, $desc, $story_img, $story_db_id, $project_id);
                } else {
                    $stmt = $conn->prepare("UPDATE project_success_stories SET subject_name = ?, subject_description = ? WHERE id = ? AND project_id = ?");
                    $stmt->bind_param("ssii", $s_name, $desc, $story_db_id, $project_id);
                }
                $stmt->execute();
                $stmt->close();
            } else {
                // Insert New
                $stmt = $conn->prepare("INSERT INTO project_success_stories (project_id, subject_name, subject_image, subject_description) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $project_id, $s_name, $story_img, $desc);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // -------------------------------------------------------------
    // 6. UPDATE PROJECT LEAD
    // -------------------------------------------------------------
    $lead_id = intval($_POST['lead_id'] ?? 0);
    $l_name = $_POST['lead_name'] ?? '';
    $l_role = $_POST['lead_role'] ?? '';
    $l_link = $_POST['lead_linkedin'] ?? '';

    $lead_img = null;
    if (isset($_FILES['lead_image']) && $_FILES['lead_image']['error'] === UPLOAD_ERR_OK) {
        $lead_img = uploadFile($_FILES['lead_image'], $img_dir, "lead_");
    }

    if ($lead_id > 0) {
        // Handle image deletion if requested
        if (!empty($removedItems['leadImageRemoved']) && !$lead_img) {
            $stmt = $conn->prepare("SELECT profile_photo FROM project_leads WHERE id = ? AND project_id = ?");
            $stmt->bind_param("ii", $lead_id, $project_id);
            $stmt->execute();
            $l_res = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            deletePhysicalFile($l_res['profile_photo'] ?? null);

            $stmt = $conn->prepare("UPDATE project_leads SET profile_photo = NULL WHERE id = ? AND project_id = ?");
            $stmt->bind_param("ii", $lead_id, $project_id);
            $stmt->execute();
            $stmt->close();
        }

        if ($lead_img) {
            $stmt = $conn->prepare("SELECT profile_photo FROM project_leads WHERE id = ? AND project_id = ?");
            $stmt->bind_param("ii", $lead_id, $project_id);
            $stmt->execute();
            $l_res = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            deletePhysicalFile($l_res['profile_photo'] ?? null);

            $stmt = $conn->prepare("UPDATE project_leads SET name = ?, role_designation = ?, linkedin_profile = ?, profile_photo = ? WHERE id = ? AND project_id = ?");
            $stmt->bind_param("ssssii", $l_name, $l_role, $l_link, $lead_img, $lead_id, $project_id);
        } else {
            $stmt = $conn->prepare("UPDATE project_leads SET name = ?, role_designation = ?, linkedin_profile = ? WHERE id = ? AND project_id = ?");
            $stmt->bind_param("sssii", $l_name, $l_role, $l_link, $lead_id, $project_id);
        }
        $stmt->execute();
        $stmt->close();
    } else if (!empty($l_name)) {
        $stmt = $conn->prepare("INSERT INTO project_leads (project_id, name, role_designation, profile_photo, linkedin_profile) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $project_id, $l_name, $l_role, $lead_img, $l_link);
        $stmt->execute();
        $stmt->close();
    }

// -------------------------------------------------------------
    // 7. MEDIA GALLERY (DELETIONS & NEW UPLOADS)
    // -------------------------------------------------------------
    if (!empty($removedItems['removedMediaIds'])) {
        foreach ($removedItems['removedMediaIds'] as $m_id) {
            $m_id_int = intval($m_id);
            if ($m_id_int > 0) {
                // Select both media and thumbnail to delete physical files
                $stmt = $conn->prepare("SELECT media_url, thumbnail_url FROM project_media WHERE id = ? AND project_id = ?");
                $stmt->bind_param("ii", $m_id_int, $project_id);
                $stmt->execute();
                $media_res = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                deletePhysicalFile($media_res['media_url'] ?? null);
                deletePhysicalFile($media_res['thumbnail_url'] ?? null);

                $stmt = $conn->prepare("DELETE FROM project_media WHERE id = ? AND project_id = ?");
                $stmt->bind_param("ii", $m_id_int, $project_id);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // --- A. HANDLE STANDARD IMAGES ---
    if (isset($_FILES['gallery_files'])) {
        $total = count($_FILES['gallery_files']['name']);
        for ($i = 0; $i < $total; $i++) {
            $file = [
                'name' => $_FILES['gallery_files']['name'][$i],
                'type' => $_FILES['gallery_files']['type'][$i],
                'tmp_name' => $_FILES['gallery_files']['tmp_name'][$i],
                'error' => $_FILES['gallery_files']['error'][$i],
                'size' => $_FILES['gallery_files']['size'][$i]
            ];
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $media_path = uploadFile($file, $img_dir, "gal_");
                
                if ($media_path) {
                    $stmt = $conn->prepare("INSERT INTO project_media (project_id, media_type, media_url, thumbnail_url) VALUES (?, 'image', ?, NULL)");
                    $stmt->bind_param("is", $project_id, $media_path);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    // --- B. HANDLE CHUNKED VIDEOS & THUMBNAILS ---
    if (isset($_POST['gallery_videos_temp'])) {
        foreach ($_POST['gallery_videos_temp'] as $index => $tempPath) {
            if (empty($tempPath)) continue;

            $actualTempPath = '../../../' . ltrim($tempPath, '/'); 
            $originalName = $_POST['gallery_videos_names'][$index] ?? 'video.mp4';
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueVideoName = 'gal_vid_' . uniqid() . '.' . $ext;
            $finalVideoPath = $vid_dir . $uniqueVideoName;

            if (file_exists($actualTempPath) && rename($actualTempPath, $finalVideoPath)) {
                $mediaUrlForDb = str_replace('../../../', '', $finalVideoPath);
                $thumbUrlForDb = NULL;

                if (isset($_FILES['gallery_thumbnails']['tmp_name'][$index]) && !empty($_FILES['gallery_thumbnails']['tmp_name'][$index])) {
                    $thumbUniqueName = 'thumb_' . uniqid() . '.jpg';
                    $thumbDestPath = $img_dir . $thumbUniqueName;
                    if (move_uploaded_file($_FILES['gallery_thumbnails']['tmp_name'][$index], $thumbDestPath)) {
                        $thumbUrlForDb = str_replace('../../../', '', $thumbDestPath);
                    }
                }

                $stmt = $conn->prepare("INSERT INTO project_media (project_id, media_type, media_url, thumbnail_url) VALUES (?, 'video', ?, ?)");
                $stmt->bind_param("iss", $project_id, $mediaUrlForDb, $thumbUrlForDb);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Commit all changes
    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Project updated successfully']);

} catch (Exception $e) {
    if (isset($conn)) mysqli_rollback($conn);
    
    echo json_encode([
        'success' => false, 
        'message' => 'Database update error: ' . $e->getMessage()
    ]);
}
?>