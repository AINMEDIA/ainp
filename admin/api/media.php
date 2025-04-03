<?php
// Include database configuration
require_once 'config.php';

// Set headers for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    sendJsonResponse(['success' => false, 'message' => 'Database connection failed'], 500);
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different request methods
switch ($method) {
    case 'GET':
        // Get all media or search media
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        
        try {
            $query = "SELECT * FROM media";
            $params = [];
            
            // Add search condition if search term is provided
            if (!empty($searchTerm)) {
                $query .= " WHERE title LIKE :search OR description LIKE :search";
                $searchParam = "%$searchTerm%";
                $params[':search'] = $searchParam;
            }
            
            // Add type filter if type is provided
            if (!empty($type)) {
                if (empty($searchTerm)) {
                    $query .= " WHERE type = :type";
                } else {
                    $query .= " AND type = :type";
                }
                $params[':type'] = $type;
            }
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            sendJsonResponse(['success' => true, 'media' => $media]);
        } catch (PDOException $e) {
            sendJsonResponse(['success' => false, 'message' => 'Error fetching media: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'POST':
        // Create new media
        $data = getJsonRequestBody();
        
        // Validate required fields
        if (!isset($data['title']) || !isset($data['type']) || !isset($data['url'])) {
            sendJsonResponse(['success' => false, 'message' => 'Missing required fields'], 400);
        }
        
        try {
            // Insert new media
            $stmt = $conn->prepare("INSERT INTO media (title, description, type, url, created_at, updated_at) VALUES (:title, :description, :type, :url, NOW(), NOW())");
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':type', $data['type']);
            $stmt->bindParam(':url', $data['url']);
            
            $stmt->execute();
            
            // Get the new media ID
            $mediaId = $conn->lastInsertId();
            
            sendJsonResponse(['success' => true, 'message' => 'Media created successfully', 'mediaId' => $mediaId]);
        } catch (PDOException $e) {
            sendJsonResponse(['success' => false, 'message' => 'Error creating media: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'PUT':
        // Update existing media
        $data = getJsonRequestBody();
        
        // Validate required fields
        if (!isset($data['id'])) {
            sendJsonResponse(['success' => false, 'message' => 'Media ID is required'], 400);
        }
        
        try {
            // Build update query dynamically based on provided fields
            $updateFields = [];
            $params = [];
            
            if (isset($data['title'])) {
                $updateFields[] = "title = :title";
                $params[':title'] = $data['title'];
            }
            
            if (isset($data['description'])) {
                $updateFields[] = "description = :description";
                $params[':description'] = $data['description'];
            }
            
            if (isset($data['type'])) {
                $updateFields[] = "type = :type";
                $params[':type'] = $data['type'];
            }
            
            if (isset($data['url'])) {
                $updateFields[] = "url = :url";
                $params[':url'] = $data['url'];
            }
            
            // Always update the updated_at timestamp
            $updateFields[] = "updated_at = NOW()";
            
            if (empty($updateFields)) {
                sendJsonResponse(['success' => false, 'message' => 'No fields to update'], 400);
            }
            
            // Add ID to params
            $params[':id'] = $data['id'];
            
            // Build and execute query
            $query = "UPDATE media SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            sendJsonResponse(['success' => true, 'message' => 'Media updated successfully']);
        } catch (PDOException $e) {
            sendJsonResponse(['success' => false, 'message' => 'Error updating media: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'DELETE':
        // Delete media
        $mediaId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$mediaId) {
            sendJsonResponse(['success' => false, 'message' => 'Media ID is required'], 400);
        }
        
        try {
            $stmt = $conn->prepare("DELETE FROM media WHERE id = :id");
            $stmt->bindParam(':id', $mediaId);
            $stmt->execute();
            
            sendJsonResponse(['success' => true, 'message' => 'Media deleted successfully']);
        } catch (PDOException $e) {
            sendJsonResponse(['success' => false, 'message' => 'Error deleting media: ' . $e->getMessage()], 500);
        }
        break;
        
    default:
        sendJsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        break;
}
?> 