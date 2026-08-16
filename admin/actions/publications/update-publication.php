<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

// Ensure upload directories exist
$doc_dir = '../../../assets/media/docs/publications/';
$img_dir = '../../../assets/media/img/publications/';
if (!file_exists($doc_dir)) mkdir($doc_dir, 0777, true);
if (!file_exists($img_dir)) mkdir($img_dir, 0777, true);

// Helper function to upload files (PDFs moved directly, images converted to WebP)
function uploadFile($file, $target_dir, $prefix = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    
    $mime = mime_content_type($file['tmp_name']);
    $is_pdf = ($mime === 'application/pdf');

    // 1. PDF Handling
    if ($is_pdf) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (empty($ext)) $ext = 'pdf';
        
        $filename = $prefix . uniqid() . '.' . $ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return str_replace('../../../', '', $target_file);
        }
        return null;
    } 
    
    // 2. Image Handling (Convert to WebP)
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
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (empty($ext)) $ext = 'jpg';
                $fallback_target = $target_dir . $prefix . uniqid() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $fallback_target)) {
                    return str_replace('../../../', '', $fallback_target);
                }
                return null;
        }

        if ($image !== false && $image !== null) {
            $success = imagewebp($image, $target_file, 80);
            imagedestroy($image);
            
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

    // Validate ID
    if (empty($_POST['id'])) {
        throw new Exception("Publication ID is required for update.");
    }
    $pub_id = intval($_POST['id']);

    // Fetch existing record
    $fetch_stmt = $conn->prepare("SELECT file_url, cover_image, status, publish_date FROM publications WHERE id = ?");
    $fetch_stmt->bind_param("i", $pub_id);
    $fetch_stmt->execute();
    $existing = $fetch_stmt->get_result()->fetch_assoc();
    $fetch_stmt->close();

    if (!$existing) {
        throw new Exception("Publication not found.");
    }

    // Process Basic Fields
    $title = $_POST['title'] ?? 'Untitled';
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $description = $_POST['description'] ?? '';
    $is_custom_cover = isset($_POST['is_custom_cover']) ? intval($_POST['is_custom_cover']) : 0;
    
    // Status & Publish Date Handling
    $raw_status = $_POST['status'] ?? $existing['status'];
    $allowed_statuses = ['draft', 'published', 'archived'];
    $status = in_array($raw_status, $allowed_statuses) ? $raw_status : 'draft';

    date_default_timezone_set('Asia/Colombo');
    $publish_date = $existing['publish_date'];
    
    // Set publish_date if newly published; clear it if downgraded to draft
    if ($status === 'published' && empty($publish_date)) {
        $publish_date = date('Y-m-d');
    } else if ($status === 'draft') {
        $publish_date = null;
    }

    // Process PDF Document
    $is_pdf_removed = isset($_POST['is_pdf_removed']) && $_POST['is_pdf_removed'] == '1';
    $pdf_path = $existing['file_url'];

    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $new_pdf = uploadFile($_FILES['pdf_file'], $doc_dir, 'pub_');
        if (!$new_pdf) throw new Exception("Failed to upload new PDF document.");
        
        // Remove old PDF file from disk
        if (!empty($existing['file_url']) && file_exists('../../../' . $existing['file_url'])) {
            @unlink('../../../' . $existing['file_url']);
        }
        $pdf_path = $new_pdf;
    } else if ($is_pdf_removed) {
        if (!empty($existing['file_url']) && file_exists('../../../' . $existing['file_url'])) {
            @unlink('../../../' . $existing['file_url']);
        }
        $pdf_path = '';
    }

    // Process Cover Image
    $is_custom_cover_removed = isset($_POST['is_custom_cover_removed']) && $_POST['is_custom_cover_removed'] == '1';
    $cover_path = $existing['cover_image'];

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $new_cover = uploadFile($_FILES['cover_image'], $img_dir, 'cover_');
        if ($new_cover) {
            // Remove old cover file from disk
            if (!empty($existing['cover_image']) && file_exists('../../../' . $existing['cover_image'])) {
                @unlink('../../../' . $existing['cover_image']);
            }
            $cover_path = $new_cover;
        }
    } else if ($is_custom_cover_removed) {
        if (!empty($existing['cover_image']) && file_exists('../../../' . $existing['cover_image'])) {
            @unlink('../../../' . $existing['cover_image']);
        }
        $cover_path = null;
        $is_custom_cover = 0;
    }

    // Execute SQL Update
    $update_stmt = $conn->prepare("
        UPDATE publications 
        SET title = ?, 
            description = ?, 
            category_id = ?, 
            cover_image = ?, 
            is_custom_cover = ?, 
            file_url = ?, 
            status = ?, 
            publish_date = ? 
        WHERE id = ?
    ");
    
    $update_stmt->bind_param(
        "ssisisssi", 
        $title, 
        $description, 
        $category_id, 
        $cover_path, 
        $is_custom_cover, 
        $pdf_path, 
        $status, 
        $publish_date, 
        $pub_id
    );
    
    $update_stmt->execute();
    $update_stmt->close();

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Publication updated successfully']);

} catch (Exception $e) {
    if (isset($conn)) mysqli_rollback($conn);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>