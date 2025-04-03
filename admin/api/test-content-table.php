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

    // Check content table structure
    $stmt = $conn->query("DESCRIBE content");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['data']['structure'] = $columns;

    // Check content data
    $stmt = $conn->query("SELECT * FROM content");
    $content = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['data']['content'] = $content;

    // Check for required content sections
    $requiredSections = [
        'hero',
        'services',
        'contact'
    ];

    $missingSections = [];
    foreach ($requiredSections as $section) {
        $found = false;
        foreach ($content as $row) {
            if ($row['section'] === $section) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $missingSections[] = $section;
        }
    }

    $response['data']['missing_sections'] = $missingSections;

    // Check content by section
    $contentBySection = [];
    foreach ($content as $row) {
        if (!isset($contentBySection[$row['section']])) {
            $contentBySection[$row['section']] = [];
        }
        $contentBySection[$row['section']][] = $row;
    }
    $response['data']['content_by_section'] = $contentBySection;

    $response['success'] = true;
    $response['message'] = 'Content table test completed successfully';
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit;
?> 