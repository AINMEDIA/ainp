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

    // Check settings table structure
    $stmt = $conn->query("DESCRIBE settings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['data']['structure'] = $columns;

    // Check settings data
    $stmt = $conn->query("SELECT * FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['data']['settings'] = $settings;

    // Check for required settings
    $requiredSettings = [
        'site_name',
        'site_description',
        'site_logo',
        'site_email',
        'site_phone',
        'site_address'
    ];

    $missingSettings = [];
    foreach ($requiredSettings as $setting) {
        $found = false;
        foreach ($settings as $row) {
            if ($row['key'] === $setting) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $missingSettings[] = $setting;
        }
    }

    $response['data']['missing_settings'] = $missingSettings;

    $response['success'] = true;
    $response['message'] = 'Settings table test completed successfully';
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit;
?> 