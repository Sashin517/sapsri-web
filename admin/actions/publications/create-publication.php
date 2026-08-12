<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

// Ensure directories exist
$doc_dir = '../../../assets/media/docs/publications/';
$img_dir = '../../../assets/media/img/publications/';
if (!file_exists($doc_dir)) mkdir($doc_dir, 0777, true);
if (!file_exists($img_dir)) mkdir($img_dir, 0777, true);

// Helper function
function uploadFile($file, $target_dir, $prefix = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if(empty($ext)) $ext = 'jpg'; // Fallback for blob images
    
    $filename = $prefix . uniqid() . '.' . $ext;
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return str_replace('../../../', '', $target_file);
    }
    return null; // Fixed the stray 's' here
}

try {
    Database::setUpConnection();
    $conn = Database::$connection;
    mysqli_begin_transaction($conn);

    $title = $_POST['title'] ?? 'Untitled';
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $description = $_POST['description'] ?? '';
    
    $raw_status = $_POST['status'] ?? 'draft';
    $status = ($raw_status === 'published') ? 'published' : 'draft';
    
    $is_custom_cover = isset($_POST['is_custom_cover']) ? intval($_POST['is_custom_cover']) : 0;
    $created_by = $_SESSION['admin_id'] ?? 6;

    // Automatic Publish Date
    date_default_timezone_set('Asia/Colombo');
    $publish_date = ($status === 'published') ? date('Y-m-d') : null;

    // 1. Upload PDF File
    $pdf_path = '';
    if (isset($_FILES['pdf_file'])) {
        $pdf_path = uploadFile($_FILES['pdf_file'], $doc_dir, 'pub_');
        if (!$pdf_path) throw new Exception("Failed to upload PDF document.");
    } else {
        throw new Exception("PDF document is required.");
    }

    // 2. Upload Cover Image (Whether from custom input or extracted blob)
    $cover_path = null;
    if (isset($_FILES['cover_image'])) {
        $cover_path = uploadFile($_FILES['cover_image'], $img_dir, 'cover_');
    }

    // 3. Insert into `publications`
    // Updated bind string to perfectly match the data types: 
    // s(title), s(description), i(category_id), s(cover_path), i(is_custom_cover), s(pdf_path), s(status), s(publish_date), i(created_by)
    $stmt = $conn->prepare("INSERT INTO publications (title, description, category_id, cover_image, is_custom_cover, file_url, status, publish_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("ssisisssi", $title, $description, $category_id, $cover_path, $is_custom_cover, $pdf_path, $status, $publish_date, $created_by);
    
    $stmt->execute();
    $stmt->close();

    mysqli_commit($conn);
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($conn)) mysqli_rollback($conn);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>