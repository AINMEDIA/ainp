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

    // Check if database exists
    $stmt = $conn->query("SELECT DATABASE()");
    $database = $stmt->fetchColumn();
    $response['data']['database'] = $database;

    // Check if settings table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'settings'");
    if ($stmt->rowCount() === 0) {
        $response['data']['settings_table'] = 'Settings table does not exist';
    } else {
        $response['data']['settings_table'] = 'Settings table exists';
    }

    // Check if content table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'content'");
    if ($stmt->rowCount() === 0) {
        $response['data']['content_table'] = 'Content table does not exist';
    } else {
        $response['data']['content_table'] = 'Content table exists';
    }

    // Check PHP version and extensions
    $response['data']['php_version'] = PHP_VERSION;
    $response['data']['extensions'] = get_loaded_extensions();

    $response['success'] = true;
    $response['message'] = 'Database test completed successfully';
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit;
?> 