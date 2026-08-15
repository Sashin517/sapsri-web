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
            // Strip any leading '../' so we can accurately point to the root from this directory
            $cleanPath = preg_replace('/^(\.\.\/)+/', '', $filePath);
            $absolutePath = "../../../" . $cleanPath;
            if (file_exists($absolutePath) && !is_dir($absolutePath)) {
                unlink($absolutePath);
            }
        }
    }

    // 1. Delete Main Cover Image
    $stmt = $conn->prepare("SELECT cover_image FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        deletePhysicalFile($row['cover_image']);
    }

    // 2. Delete Gallery Media Files
    $mediaStmt = $conn->prepare("SELECT media_url FROM project_media WHERE project_id = ?");
    $mediaStmt->bind_param("i", $id);
    $mediaStmt->execute();
    $mediaRes = $mediaStmt->get_result();
    while($mRow = $mediaRes->fetch_assoc()) {
        deletePhysicalFile($mRow['media_url']);
    }

    // 3. Delete from Database (Cascading will automatically delete metrics, stories, leads, and media rows)
    $delStmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $delStmt->bind_param("i", $id);
    
    if ($delStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error during deletion']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>