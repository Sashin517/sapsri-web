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
            imagedestroy($image); // Free up server RAM
            
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

    $title = $_POST['title'] ?? 'Untitled Post';
    $impact_area = $_POST['impact_area'] ?? null;
    $content = $_POST['content'] ?? '';
    
    // Status Guard
    $raw_status = $_POST['status'] ?? 'draft';
    $status = ($raw_status === 'published' || $raw_status === 'archived') ? $raw_status : 'draft';

    // automatic date time submit
    date_default_timezone_set('Asia/Colombo'); // Ensure Sri Lanka timezone
    
    $publish_date = null;
    // If the button clicked was 'Publish', automatically set the exact current time
    if ($status === 'published') {
        $publish_date = date('Y-m-d H:i:s');
    }

    $created_by = $_SESSION['admin_id'] ?? 6;

    // 1. Upload Cover Image
    $cover_path = null;
    if (isset($_FILES['cover_image'])) {
        $cover_path = uploadFile($_FILES['cover_image'], $img_dir, 'cover_');
    }

    // 2. Insert into `posts`
    $stmt = $conn->prepare("INSERT INTO posts (title, cover_image, content, status, created_by, publish_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $title, $cover_path, $content, $status, $created_by, $publish_date);
    $stmt->execute();
    $post_id = $conn->insert_id;
    $stmt->close();

    // 3. Insert into `post_impact_areas`
    if (!empty($impact_area)) {
        $stmt = $conn->prepare("INSERT INTO post_impact_areas (post_id, impact_area_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $post_id, $impact_area);
        $stmt->execute();
        $stmt->close();
    }

    // 4. Handle Media Gallery (Sorting Images vs Videos)
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
                $stmt = $conn->prepare("INSERT INTO post_media (post_id, media_type, media_url) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $post_id, $type_enum, $media_path);
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