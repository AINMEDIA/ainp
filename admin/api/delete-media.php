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

    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id'])) {
        throw new Exception('Media ID is required');
    }

    // Get file URL before deleting
    $stmt = $conn->prepare("SELECT url FROM media WHERE id = ?");
    $stmt->execute([$data['id']]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$media) {
        throw new Exception('Media not found');
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM media WHERE id = ?");
    $stmt->execute([$data['id']]);

    // Delete file
    if (file_exists('../' . $media['url'])) {
        unlink('../' . $media['url']);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Media deleted successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 