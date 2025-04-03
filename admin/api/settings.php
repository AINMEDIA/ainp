<?php
// Include database configuration
require_once 'config.php';

// Set headers for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    $conn = getDbConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    switch ($method) {
        case 'GET':
            // Get all settings
            try {
                $stmt = $conn->query("SELECT * FROM settings");
                $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Convert to key-value format
                $settingsArray = [];
                foreach ($settings as $setting) {
                    $settingsArray[$setting['key']] = $setting['value'];
                }
                
                $response['success'] = true;
                $response['data'] = $settingsArray;
            } catch (PDOException $e) {
                $response['message'] = 'Error fetching settings: ' . $e->getMessage();
                http_response_code(500);
            }
            break;

        case 'POST':
            // Create new setting
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['key']) || !isset($data['value'])) {
                $response['message'] = 'Missing required fields';
                http_response_code(400);
                break;
            }
            
            try {
                // Check if setting already exists
                $stmt = $conn->prepare("SELECT id FROM settings WHERE `key` = ?");
                $stmt->execute([$data['key']]);
                
                if ($stmt->rowCount() > 0) {
                    $response['message'] = 'Setting already exists';
                    http_response_code(409);
                    break;
                }
                
                // Insert new setting
                $stmt = $conn->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?)");
                $stmt->execute([$data['key'], $data['value']]);
                
                $response['success'] = true;
                $response['message'] = 'Setting created successfully';
                $response['data'] = ['id' => $conn->lastInsertId()];
            } catch (PDOException $e) {
                $response['message'] = 'Error creating setting: ' . $e->getMessage();
                http_response_code(500);
            }
            break;

        case 'PUT':
            // Update existing setting
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['key']) || !isset($data['value'])) {
                $response['message'] = 'Missing required fields';
                http_response_code(400);
                break;
            }
            
            try {
                // Check if setting exists
                $stmt = $conn->prepare("SELECT id FROM settings WHERE `key` = ?");
                $stmt->execute([$data['key']]);
                
                if ($stmt->rowCount() === 0) {
                    // Create new setting if it doesn't exist
                    $stmt = $conn->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?)");
                    $stmt->execute([$data['key'], $data['value']]);
                    $response['message'] = 'Setting created successfully';
                } else {
                    // Update existing setting
                    $stmt = $conn->prepare("UPDATE settings SET `value` = ? WHERE `key` = ?");
                    $stmt->execute([$data['value'], $data['key']]);
                    $response['message'] = 'Setting updated successfully';
                }
                
                $response['success'] = true;
                $response['data'] = ['key' => $data['key'], 'value' => $data['value']];
            } catch (PDOException $e) {
                $response['message'] = 'Error updating setting: ' . $e->getMessage();
                http_response_code(500);
            }
            break;

        default:
            $response['message'] = 'Method not allowed';
            http_response_code(405);
    }
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit;
?> 