<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

// Ensure directories exist
$img_dir = '../../../assets/media/img/posts/';
$vid_dir = '../../../assets/media/videos/posts/';
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
    else if (strpos($mime, 'image/') === 0) {
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
                // Fallback for unsupported image types
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (empty($ext)) $ext = 'jpg';
                $fallback_target = $target_dir . $prefix . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $fallback_target)) {
                    return str_replace('../../../', '', $fallback_target);
                }
                return null;
        }

        // Save the image as WebP with 80% quality
        if ($image !== false && $image !== null) {
            $success = imagewebp($image, $target_file, 80);
            unset($image);
            
            
            if ($success) {
                return str_replace('../../../', '', $target_file);
            }
        }
        return null;
    }
    
    return null;
}

try {
    Database::setUpConnection();
    $conn = Database::$connection;
    
    mysqli_begin_transaction($conn);

    $post_id = $_POST['id'] ?? null;
    if (!$post_id) {
        throw new Exception("Post ID is required for update.");
    }

    // Dynamic field building array for SQL update statement
    $update_fields = [];
    $params = [];
    $types = "";

    // Title update (optional fallback to current or updated if set)
    if (isset($_POST['title'])) {
        $update_fields[] = "title = ?";
        $params[] = $_POST['title'];
        $types .= "s";
    }

    // Content update (Only update if explicitly set in POST)
    if (isset($_POST['content'])) {
        $update_fields[] = "content = ?";
        $params[] = $_POST['content'];
        $types .= "s";
    }

    // Parse boolean for cover image deletion
    $is_cover_deleted = filter_var($_POST['is_cover_deleted'] ?? false, FILTER_VALIDATE_BOOLEAN);

    // Cover Image update logic
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $cover_path = uploadFile($_FILES['cover_image'], $img_dir, 'cover_');
        $update_fields[] = "cover_image = ?";
        $params[] = $cover_path;
        $types .= "s";
    } elseif ($is_cover_deleted) {
        $update_fields[] = "cover_image = NULL";
    }

    // Status & Publish Date update logic
    if (isset($_POST['status'])) {
        $raw_status = $_POST['status'];
        $status = ($raw_status === 'published' || $raw_status === 'archived') ? $raw_status : 'draft';
        
        $update_fields[] = "status = ?";
        $params[] = $status;
        $types .= "s";

        date_default_timezone_set('Asia/Colombo');
        if ($status === 'published') {
            $update_fields[] = "publish_date = ?";
            $params[] = date('Y-m-d H:i:s');
            $types .= "s";
        }
    }

    // Execute update query only if there are fields to update
    if (!empty($update_fields)) {
        $query = "UPDATE posts SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $params[] = (int)$post_id;
        $types .= "i";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();
    }

    // --- CHANGED: Update `post_impact_areas` (looping through array) ---
    if (isset($_POST['impact_area'])) {
        $impact_areas = $_POST['impact_area'];
        if (!is_array($impact_areas)) {
            $impact_areas = [$impact_areas]; // Fallback
        }
        
        // Always delete old associations first to ensure a clean update
        $stmt = $conn->prepare("DELETE FROM post_impact_areas WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->close();

        // Insert new ones
        if (!empty($impact_areas)) {
            $stmt = $conn->prepare("INSERT INTO post_impact_areas (post_id, impact_area_id) VALUES (?, ?)");
            foreach ($impact_areas as $area_id) {
                $area_id_int = intval($area_id);
                if ($area_id_int > 0) {
                    $stmt->bind_param("ii", $post_id, $area_id_int);
                    $stmt->execute();
                }
            }
            $stmt->close();
        }
    }

    // -------------------------------------------------------------
    // MEDIA GALLERY DELETIONS
    // -------------------------------------------------------------
    if (!empty($_POST['gallery_files_deleted']) && is_array($_POST['gallery_files_deleted'])) {
        $deleted_ids = array_map('intval', $_POST['gallery_files_deleted']);
        $in_clause = implode(',', $deleted_ids);
        
        if (!empty($in_clause)) {
            // Delete physical files (both media and thumbnail)
            $res = $conn->query("SELECT media_url, thumbnail_url FROM post_media WHERE id IN ($in_clause) AND post_id = $post_id");
            while ($row = $res->fetch_assoc()) {
                $file_to_delete = '../../../' . ltrim($row['media_url'] ?? '', '/');
                if (!empty($row['media_url']) && file_exists($file_to_delete) && is_file($file_to_delete)) {
                    @unlink($file_to_delete);
                }

                $thumb_to_delete = '../../../' . ltrim($row['thumbnail_url'] ?? '', '/');
                if (!empty($row['thumbnail_url']) && file_exists($thumb_to_delete) && is_file($thumb_to_delete)) {
                    @unlink($thumb_to_delete);
                }
            }

            // Delete database rows
            $conn->query("DELETE FROM post_media WHERE id IN ($in_clause) AND post_id = $post_id");
        }
    }

// -------------------------------------------------------------
    // MEDIA GALLERY DELETIONS
    // -------------------------------------------------------------
    if (!empty($_POST['gallery_files_deleted']) && is_array($_POST['gallery_files_deleted'])) {
        $deleted_ids = array_map('intval', $_POST['gallery_files_deleted']);
        $in_clause = implode(',', $deleted_ids);
        
        if (!empty($in_clause)) {
            // Delete physical files (both media and thumbnail)
            $res = $conn->query("SELECT media_url, thumbnail_url FROM post_media WHERE id IN ($in_clause) AND post_id = $post_id");
            while ($row = $res->fetch_assoc()) {
                $file_to_delete = '../../../' . ltrim($row['media_url'] ?? '', '/');
                if (!empty($row['media_url']) && file_exists($file_to_delete) && is_file($file_to_delete)) {
                    @unlink($file_to_delete);
                }

                $thumb_to_delete = '../../../' . ltrim($row['thumbnail_url'] ?? '', '/');
                if (!empty($row['thumbnail_url']) && file_exists($thumb_to_delete) && is_file($thumb_to_delete)) {
                    @unlink($thumb_to_delete);
                }
            }

            // Delete database rows
            $conn->query("DELETE FROM post_media WHERE id IN ($in_clause) AND post_id = $post_id");
        }
    }

    // -------------------------------------------------------------
    // MEDIA GALLERY (NEW UPLOADS)
    // -------------------------------------------------------------
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
                    $stmt = $conn->prepare("INSERT INTO post_media (post_id, media_type, media_url, thumbnail_url) VALUES (?, 'image', ?, NULL)");
                    $stmt->bind_param("is", $post_id, $media_path);
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

                $stmt = $conn->prepare("INSERT INTO post_media (post_id, media_type, media_url, thumbnail_url) VALUES (?, 'video', ?, ?)");
                $stmt->bind_param("iss", $post_id, $mediaUrlForDb, $thumbUrlForDb);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    
    mysqli_commit($conn);
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($conn)) mysqli_rollback($conn);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>