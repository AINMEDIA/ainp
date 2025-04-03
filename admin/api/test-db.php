<?php
// Include database configuration
require_once 'config.php';

// Set headers for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Test database connection
    $conn = getDbConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    $response['data']['connection'] = 'Database connection successful';

    // Check if settings table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'settings'");
    if ($stmt->rowCount() === 0) {
        // Create settings table if it doesn't exist
        $conn->exec("CREATE TABLE settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(255) NOT NULL UNIQUE,
            `value` TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        $response['data']['table'] = 'Settings table created';
    } else {
        $response['data']['table'] = 'Settings table exists';
    }

    // Check table structure
    $stmt = $conn->query("DESCRIBE settings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['data']['structure'] = $columns;

    // Check if there are any settings
    $stmt = $conn->query("SELECT * FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['data']['settings'] = $settings;

    $response['success'] = true;
    $response['message'] = 'Database test completed successfully';
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit;
?> 