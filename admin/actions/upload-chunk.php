<?php
header('Content-Type: application/json; charset=utf-8');

// Disable HTML error output to prevent breaking the JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    // 1. Verify a chunk was actually received
    if (!isset($_FILES['chunk']) || $_FILES['chunk']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No chunk received or an upload error occurred.');
    }

    // 2. Read the metadata sent from JavaScript
    $chunkIndex = isset($_POST['chunkIndex']) ? intval($_POST['chunkIndex']) : 0;
    $totalChunks = isset($_POST['totalChunks']) ? intval($_POST['totalChunks']) : 1;
    
    // Sanitize the filename to prevent directory traversal attacks
    $fileName = isset($_POST['fileName']) ? preg_replace('/[^a-zA-Z0-9._-]/', '_', $_POST['fileName']) : 'upload.tmp';

    // 3. Define the temporary upload directory
    // Since this file is in admin/actions/, we go up two levels to reach the root media folder
    $tempDir = '../../media/temp/'; 

    // Create the temp directory if it doesn't exist yet
    if (!is_dir($tempDir)) {
        if (!mkdir($tempDir, 0755, true)) {
            throw new Exception('Failed to create temporary upload directory.');
        }
    }

    $filePath = $tempDir . $fileName;

    // 4. Open the destination file 
    // 'wb' (write) for the first chunk to create a fresh file
    // 'ab' (append) for all subsequent chunks to add data to the end
    $out = fopen($filePath, $chunkIndex == 0 ? 'wb' : 'ab');
    if (!$out) {
        throw new Exception('Failed to open the output file on the server.');
    }

    // 5. Open the uploaded chunk piece
    $in = fopen($_FILES['chunk']['tmp_name'], 'rb');
    if (!$in) {
        fclose($out);
        throw new Exception('Failed to open the uploaded chunk.');
    }

    // 6. Stream the chunk data into the destination file
    while ($buffer = fread($in, 4096)) {
        fwrite($out, $buffer);
    }

    // Clean up memory and close files
    fclose($in);
    fclose($out);

    // 7. Check if this is the final piece of the file
    $isComplete = ($chunkIndex == $totalChunks - 1);
    
    $response = [
        'success' => true,
        'message' => 'Chunk ' . ($chunkIndex + 1) . '/' . $totalChunks . ' uploaded successfully.',
        'is_complete' => $isComplete
    ];

    // If the file is 100% finished, return the path so the frontend can attach it to the form
    if ($isComplete) {
        // We return the path starting from 'media/' so it's easy for your other scripts to locate it
        $response['temp_path'] = 'media/temp/' . $fileName; 
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>