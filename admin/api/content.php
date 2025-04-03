<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

// Check authentication for non-GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    // Authentication check would go here
    // For now, we'll skip it for demo purposes
}

$method = $_SERVER['REQUEST_METHOD'];
$content_type = $_GET['type'] ?? '';

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
            // Get all content or search content
            $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
            $section = isset($_GET['section']) ? $_GET['section'] : '';
            
            try {
                $query = "SELECT * FROM content";
                $params = [];
                
                // Add search condition if search term is provided
                if (!empty($searchTerm)) {
                    $query .= " WHERE title LIKE :search OR content LIKE :search";
                    $searchParam = "%$searchTerm%";
                    $params[':search'] = $searchParam;
                }
                
                // Add section filter if section is provided
                if (!empty($section)) {
                    if (empty($searchTerm)) {
                        $query .= " WHERE section = :section";
                    } else {
                        $query .= " AND section = :section";
                    }
                    $params[':section'] = $section;
                }
                
                $stmt = $conn->prepare($query);
                $stmt->execute($params);
                $content = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $response['success'] = true;
                $response['data'] = $content;
            } catch (PDOException $e) {
                $response['message'] = 'Error fetching content: ' . $e->getMessage();
            }
            break;

        case 'POST':
            // Create new content
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            if (!isset($data['title']) || !isset($data['content']) || !isset($data['section'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                exit;
            }
            
            try {
                // Insert new content
                $stmt = $conn->prepare("INSERT INTO content (title, content, section, created_at, updated_at) VALUES (:title, :content, :section, NOW(), NOW())");
                $stmt->bindParam(':title', $data['title']);
                $stmt->bindParam(':content', $data['content']);
                $stmt->bindParam(':section', $data['section']);
                
                $stmt->execute();
                
                // Get the new content ID
                $contentId = $conn->lastInsertId();
                
                $response['success'] = true;
                $response['message'] = 'Content created successfully';
                $response['data'] = ['contentId' => $contentId];
            } catch (PDOException $e) {
                $response['message'] = 'Error creating content: ' . $e->getMessage();
            }
            break;

        case 'PUT':
            // Update existing content
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Content ID is required']);
                exit;
            }
            
            try {
                // Build update query dynamically based on provided fields
                $updateFields = [];
                $params = [];
                
                if (isset($data['title'])) {
                    $updateFields[] = "title = :title";
                    $params[':title'] = $data['title'];
                }
                
                if (isset($data['content'])) {
                    $updateFields[] = "content = :content";
                    $params[':content'] = $data['content'];
                }
                
                if (isset($data['section'])) {
                    $updateFields[] = "section = :section";
                    $params[':section'] = $data['section'];
                }
                
                // Always update the updated_at timestamp
                $updateFields[] = "updated_at = NOW()";
                
                if (empty($updateFields)) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'No fields to update']);
                    exit;
                }
                
                // Add ID to params
                $params[':id'] = $data['id'];
                
                // Build and execute query
                $query = "UPDATE content SET " . implode(', ', $updateFields) . " WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->execute($params);
                
                $response['success'] = true;
                $response['message'] = 'Content updated successfully';
            } catch (PDOException $e) {
                $response['message'] = 'Error updating content: ' . $e->getMessage();
            }
            break;

        case 'DELETE':
            // Delete content
            $contentId = isset($_GET['id']) ? $_GET['id'] : null;
            
            if (!$contentId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Content ID is required']);
                exit;
            }
            
            try {
                $stmt = $conn->prepare("DELETE FROM content WHERE id = :id");
                $stmt->bindParam(':id', $contentId);
                $stmt->execute();
                
                $response['success'] = true;
                $response['message'] = 'Content deleted successfully';
            } catch (PDOException $e) {
                $response['message'] = 'Error deleting content: ' . $e->getMessage();
            }
            break;

        default:
            $response['message'] = 'Method not allowed';
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response); 