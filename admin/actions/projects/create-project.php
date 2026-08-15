<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

// Ensure directories exist
$img_dir = '../../../assets/media/img/projects/';
$vid_dir = '../../../assets/media/videos/projects/';
if (!file_exists($img_dir)) mkdir($img_dir, 0777, true);
if (!file_exists($vid_dir)) mkdir($vid_dir, 0777, true);

// Upgraded Helper function to handle WebP Conversion
function uploadFile($file, $target_dir, $prefix = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    
    $mime = mime_content_type($file['tmp_name']);
    $is_video = strpos($mime, 'video/') === 0;

    // 1. If it's a video, bypass conversion and just move it
    if ($is_video) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . uniqid() . '.' . $ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return str_replace('../../../', '', $target_file);
        }
        return null;
    } 
    
    // 2. If it's an image, convert to WebP
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
                // Fallback for unsupported image types (just move it)
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fallback_target = $target_dir . $prefix . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $fallback_target)) {
                    return str_replace('../../../', '', $fallback_target);
                }
                return null;
        }

        // Save the image as WebP with 80% quality
        if ($image !== false && $image !== null) {
            $success = imagewebp($image, $target_file, 80);
            imagedestroy($image); // Free up server RAM
            
            if ($success) {
                return str_replace('../../../', '', $target_file);
            }
        }
        return null;
    }
}

try {
    Database::setUpConnection();
    $conn = Database::$connection;
    
    // Start SQL Transaction! If anything fails below here, NOTHING saves to the DB.
    mysqli_begin_transaction($conn);

    $title = $_POST['title'] ?? 'Untitled';
    $phase = $_POST['phase'] ?? 'ongoing';
    $description = $_POST['full_description'] ?? '';
    $status = $_POST['status'] ?? 'draft';
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    
    // --- CHANGED: Retrieve impact areas as an array ---
    $impact_areas = $_POST['impact_area'] ?? [];
    if (!is_array($impact_areas) && !empty($impact_areas)) {
        $impact_areas = [$impact_areas]; // Fallback in case a single string is passed
    }

    // Safety check: if phase is ongoing, ensure end_date is strictly null
    if ($phase === 'ongoing') {
        $end_date = null;
    }
    $created_by = $_SESSION['admin_id'] ?? 1; // Default to 1 if not set

    // 1. Upload Cover Image
    $cover_path = null;
    if (isset($_FILES['cover_image'])) {
        $cover_path = uploadFile($_FILES['cover_image'], $img_dir, 'cover_');
    }

    // 2. Insert into `projects`
    $stmt = $conn->prepare("INSERT INTO projects (title, cover_image, full_description, status, project_phase, start_date, end_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Bind the 8 parameters (s = string, i = integer)
    $stmt->bind_param("sssssssi", $title, $cover_path, $description, $status, $phase, $start_date, $end_date, $created_by);
    
    $stmt->execute();
    $project_id = $conn->insert_id;
    $stmt->close();

    // 3. Insert into `project_impact_areas` (CHANGED: Now loops through array)
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

    // 4. Handle Metrics (Section 1 & 2)
    for ($sec = 1; $sec <= 2; $sec++) {
        $sec_img_key = 'sec' . $sec . '_image';
        $sec_img_path = null;
        if (isset($_FILES[$sec_img_key])) {
            $sec_img_path = uploadFile($_FILES[$sec_img_key], $img_dir, "s{$sec}_");
        }

        if (isset($_POST["sec{$sec}_metrics"])) {
            foreach ($_POST["sec{$sec}_metrics"] as $index => $metricData) {
                $icon_path = null;
                $file_key = "sec{$sec}_metrics";
                if (isset($_FILES[$file_key]['name'][$index]['icon'])) {
                    // Restructure the nested $_FILES array for the helper
                    $file = [
                        'name' => $_FILES[$file_key]['name'][$index]['icon'],
                        'type' => $_FILES[$file_key]['type'][$index]['icon'],
                        'tmp_name' => $_FILES[$file_key]['tmp_name'][$index]['icon'],
                        'error' => $_FILES[$file_key]['error'][$index]['icon'],
                        'size' => $_FILES[$file_key]['size'][$index]['icon']
                    ];
                    $icon_path = uploadFile($file, $img_dir, "icon_");
                }
                
                $val = $metricData['value'] ?? '';
                $lbl = $metricData['label'] ?? '';
                
                $stmt = $conn->prepare("INSERT INTO project_metrics (project_id, section_number, section_image, icon_image, metric_value, metric_label) VALUES (?, ?, ?, ?, ?, ?)");
                $sec_str = (string)$sec;
                $stmt->bind_param("isssss", $project_id, $sec_str, $sec_img_path, $icon_path, $val, $lbl);
                $stmt->execute();
                $stmt->close();
                
                // Only insert the section image once per section, subsequent metrics in same section can have null section_image to save space, or repeat it.
                $sec_img_path = null; 
            }
        }
    }

    // 5. Handle Success Stories
    if (isset($_POST['stories'])) {
        foreach ($_POST['stories'] as $index => $story) {
            $story_img = null;
            if (isset($_FILES['stories']['name'][$index]['image'])) {
                $file = [
                    'name' => $_FILES['stories']['name'][$index]['image'],
                    'type' => $_FILES['stories']['type'][$index]['image'],
                    'tmp_name' => $_FILES['stories']['tmp_name'][$index]['image'],
                    'error' => $_FILES['stories']['error'][$index]['image'],
                    'size' => $_FILES['stories']['size'][$index]['image']
                ];
                $story_img = uploadFile($file, $img_dir, "story_");
            }
            
            $desc = $story['description'] ?? '';
            $s_name = $story['name'] ?? '';
            
            $stmt = $conn->prepare("INSERT INTO project_success_stories (project_id, subject_name, subject_image, subject_description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $project_id, $s_name, $story_img, $desc);
            $stmt->execute();
            $stmt->close();
        }
    }

    // 6. Handle Project Leads
    $lead_img = null;
    if (isset($_FILES['lead_image'])) {
        $lead_img = uploadFile($_FILES['lead_image'], $img_dir, "lead_");
    }
    $l_name = $_POST['lead_name'] ?? '';
    $l_role = $_POST['lead_role'] ?? '';
    $l_link = $_POST['lead_linkedin'] ?? '';
    
    if ($l_name != '') {
        $stmt = $conn->prepare("INSERT INTO project_leads (project_id, name, role_designation, profile_photo, linkedin_profile) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $project_id, $l_name, $l_role, $lead_img, $l_link);
        $stmt->execute();
        $stmt->close();
    }

    // 7. Handle Media Gallery (Sorting Images vs Videos)
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
            
            $is_video = strpos($file['type'], 'video/') === 0;
            $target = $is_video ? $vid_dir : $img_dir;
            $type_enum = $is_video ? 'video' : 'image';
            
            $media_path = uploadFile($file, $target, "gal_");
            
            if ($media_path) {
                $stmt = $conn->prepare("INSERT INTO project_media (project_id, media_type, media_url) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $project_id, $type_enum, $media_path);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // If everything succeeded without errors, commit to the database!
    mysqli_commit($conn);
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // If ANY step failed, rollback the database so nothing is saved.
    if (isset($conn)) mysqli_rollback($conn);
    
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>