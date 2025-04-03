<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $conn = getDbConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Check if table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'portfolio'");
    $tableExists = $stmt->rowCount() > 0;

    if ($tableExists) {
        // Get table structure
        $stmt = $conn->query("DESCRIBE portfolio");
        $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'message' => 'Portfolio table exists',
            'structure' => $structure
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Portfolio table does not exist'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 