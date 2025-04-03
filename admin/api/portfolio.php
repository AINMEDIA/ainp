<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $conn = getDbConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get single portfolio item
                $stmt = $conn->prepare("SELECT * FROM portfolio WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($item) {
                    echo json_encode([
                        'success' => true,
                        'item' => $item
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Portfolio item not found'
                    ]);
                }
            } else {
                // Get all portfolio items
                $stmt = $conn->query("SELECT * FROM portfolio ORDER BY created_at DESC");
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'items' => $items
                ]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['title']) || !isset($data['description']) || !isset($data['image_url'])) {
                throw new Exception('Missing required fields');
            }

            $stmt = $conn->prepare("INSERT INTO portfolio (title, description, image_url, category) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['image_url'],
                $data['category'] ?? 'uncategorized'
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Portfolio item created successfully',
                'id' => $conn->lastInsertId()
            ]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id']) || !isset($data['title']) || !isset($data['description']) || !isset($data['image_url'])) {
                throw new Exception('Missing required fields');
            }

            $stmt = $conn->prepare("UPDATE portfolio SET title = ?, description = ?, image_url = ?, category = ? WHERE id = ?");
            $stmt->execute([
                $data['title'],
                $data['description'],
                $data['image_url'],
                $data['category'] ?? 'uncategorized',
                $data['id']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Portfolio item updated successfully'
            ]);
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                throw new Exception('Portfolio item ID is required');
            }

            $stmt = $conn->prepare("DELETE FROM portfolio WHERE id = ?");
            $stmt->execute([$_GET['id']]);

            echo json_encode([
                'success' => true,
                'message' => 'Portfolio item deleted successfully'
            ]);
            break;

        default:
            throw new Exception('Method not allowed');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>