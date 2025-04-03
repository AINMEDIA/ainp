<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $conn = getDbConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    if (!isset($_FILES['files'])) {
        throw new Exception('No files uploaded');
    }

    $uploadDir = '../../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $errors = [];
    $success = true;

    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        $filename = $_FILES['files']['name'][$key];
        $filesize = $_FILES['files']['size'][$key];
        $filetype = $_FILES['files']['type'][$key];
        
        // Generate a unique filename
        $uniqueFilename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $filename);
        $filepath = 'uploads/' . $uniqueFilename;  // URL path for the database (without /ainp/ prefix)
        $fullPath = $uploadDir . $uniqueFilename;  // Full server path for file storage

        if (move_uploaded_file($tmp_name, $fullPath)) {
            // Determine file type
            $type = 'document';
            if (strpos($filetype, 'image/') === 0) {
                $type = 'image';
            } elseif (strpos($filetype, 'video/') === 0) {
                $type = 'video';
            }

            $stmt = $conn->prepare("INSERT INTO media (title, description, type, url) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $filename,
                "File size: " . formatFileSize($filesize),
                $type,
                $filepath
            ]);
        } else {
            $errors[] = "Failed to upload $filename";
            $success = false;
        }
    }

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Files uploaded successfully' : 'Some files failed to upload',
        'errors' => $errors
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}
?> 