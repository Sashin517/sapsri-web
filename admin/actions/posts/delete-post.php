<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

// Ensure the request is POST and an ID is provided
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request methodology']);
    exit;
}

$post_id = intval($_POST['id']);

try {
    Database::setUpConnection();
    $conn = Database::$connection;

    // Start SQL Transaction
    mysqli_begin_transaction($conn);

    // ==========================================
    // 1. DELETE PHYSICAL MEDIA FILES
    // ==========================================
    
    // Define the base path relative to this script's location
    $base_path = '../../../'; 

    // A. Fetch and delete the Cover Image
    $stmt_cover = $conn->prepare("SELECT cover_image FROM posts WHERE id = ?");
    $stmt_cover->bind_param("i", $post_id);
    $stmt_cover->execute();
    $result_cover = $stmt_cover->get_result();
    
    if ($row = $result_cover->fetch_assoc()) {
        if (!empty($row['cover_image'])) {
            $cover_file_path = $base_path . $row['cover_image'];
            if (file_exists($cover_file_path) && is_file($cover_file_path)) {
                unlink($cover_file_path); // Delete the physical file
            }
        }
    }
    $stmt_cover->close();

    // B. Fetch and delete all Gallery Media associated with the post
    $stmt_media = $conn->prepare("SELECT media_url FROM post_media WHERE post_id = ?");
    $stmt_media->bind_param("i", $post_id);
    $stmt_media->execute();
    $result_media = $stmt_media->get_result();
    
    while ($media_row = $result_media->fetch_assoc()) {
        if (!empty($media_row['media_url'])) {
            $media_file_path = $base_path . $media_row['media_url'];
            if (file_exists($media_file_path) && is_file($media_file_path)) {
                unlink($media_file_path); // Delete the physical file
            }
        }
    }
    $stmt_media->close();

    // ==========================================
    // 2. DELETE DATABASE RECORDS
    // ==========================================
    
    // Delete the main post record. 
    // Note: Because the database has ON DELETE CASCADE constraints for post_media and post_impact_areas, 
    // MySQL will automatically delete the related rows in those tables.
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    
    // Check if the deletion was successful
    if ($stmt->affected_rows > 0) {
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Post and associated media deleted permanently.']);
    } else {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'Post could not be found or has already been deleted.']);
    }
    
    $stmt->close();

} catch (Exception $e) {
    // If any step fails, roll back the database transaction
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    
    echo json_encode([
        'success' => false, 
        'message' => 'Server Error: ' . $e->getMessage()
    ]);
}
?>