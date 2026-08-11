<?php
session_start();
require_once '../../../includes/connection.php';

header('Content-Type: application/json');

try {
    Database::setUpConnection();
    $conn = Database::$connection;

    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $action = $_POST['action'] ?? '';
    
    if ($user_id === 0 || empty($action)) {
        throw new Exception("Invalid request parameters.");
    }

    if ($action === 'suspend') {
        $duration = $_POST['duration'] ?? '';
        if (empty($duration)) throw new Exception("Duration is required.");

        $end_date = null;
        date_default_timezone_set('Asia/Colombo');
        $now = new DateTime();

        // Calculate the un-suspend date
        switch ($duration) {
            case '24_hours': $now->modify('+1 day'); $end_date = $now->format('Y-m-d H:i:s'); break;
            case '3_days': $now->modify('+3 days'); $end_date = $now->format('Y-m-d H:i:s'); break;
            case '1_week': $now->modify('+1 week'); $end_date = $now->format('Y-m-d H:i:s'); break;
            case '1_month': $now->modify('+1 month'); $end_date = $now->format('Y-m-d H:i:s'); break;
            case 'indefinite': $end_date = null; break; // Stays null
            default: throw new Exception("Invalid duration.");
        }

        $stmt = $conn->prepare("UPDATE users SET status = 'suspended', suspension_end_date = ? WHERE id = ?");
        $stmt->bind_param("si", $end_date, $user_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'message' => 'User has been suspended.']);

    } elseif ($action === 'activate') {
        // Remove suspension and clear the date
        $stmt = $conn->prepare("UPDATE users SET status = 'active', suspension_end_date = NULL WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'message' => 'User suspension removed. Account is active.']);
    } else {
        throw new Exception("Unknown action.");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>