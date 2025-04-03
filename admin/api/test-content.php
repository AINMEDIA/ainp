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

    // Check if content table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'content'");
    if ($stmt->rowCount() === 0) {
        // Create content table if it doesn't exist
        $conn->exec("CREATE TABLE content (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            section VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        $response['data']['table'] = 'Content table created';
    } else {
        $response['data']['table'] = 'Content table exists';
    }

    // Check table structure
    $stmt = $conn->query("DESCRIBE content");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['data']['structure'] = $columns;

    // Check if there are any content items
    $stmt = $conn->query("SELECT * FROM content");
    $content = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['data']['content'] = $content;

    $response['success'] = true;
    $response['message'] = 'Content test completed successfully';
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit;
?> 