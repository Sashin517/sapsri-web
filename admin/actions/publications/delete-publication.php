<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    
    Database::setUpConnection();
    $conn = Database::$connection;
    $id = intval($_POST['id']);

    // --- Helper function to safely delete files ---
    function deletePhysicalFile($filePath) {
        if (!empty($filePath)) {
            // Strip any leading '../' to standardize the path
            $cleanPath = preg_replace('/^(\.\.\/)+/', '', $filePath);
            $absolutePath = "../../../" . $cleanPath;
            if (file_exists($absolutePath) && !is_dir($absolutePath)) {
                unlink($absolutePath);
            }
        }
    }

    // 1. Fetch the PDF file and Cover Image to delete from the server
    $stmt = $conn->prepare("SELECT file_url, cover_image FROM publications WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($row = $res->fetch_assoc()) {
        deletePhysicalFile($row['file_url']);
        deletePhysicalFile($row['cover_image']);
    }

    // 2. Delete from Database
    $delStmt = $conn->prepare("DELETE FROM publications WHERE id = ?");
    $delStmt->bind_param("i", $id);
    
    if ($delStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Publication deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error during deletion']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>