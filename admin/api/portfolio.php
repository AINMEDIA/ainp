<?php
require_once '../config/config.php';

header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit();
}

// Require login for all operations
requireLogin();

// Get all portfolio items
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $conn = getDBConnection();
        $stmt = $conn->query("SELECT * FROM portfolio_items ORDER BY created_at DESC");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendSuccessResponse($items);
    } catch (PDOException $e) {
        sendErrorResponse('Database error: ' . $e->getMessage(), 500);
    }
}

// Add new portfolio item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            sendErrorResponse('Invalid CSRF token', 403);
        }

        // Validate required fields
        if (!isset($_POST['title']) || !isset($_POST['category'])) {
            sendErrorResponse('Title and category are required');
        }

        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description'] ?? '');
        $category = sanitizeInput($_POST['category']);

        // Handle image upload
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            sendErrorResponse('Image upload is required');
        }

        $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'portfolio/');
        if (!$uploadResult['success']) {
            sendErrorResponse($uploadResult['message']);
        }

        // Insert into database
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO portfolio_items (title, description, image_url, category) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $uploadResult['fileName'], $category]);

        $itemId = $conn->lastInsertId();
        
        // Log activity
        logActivity($_SESSION['user_id'], 'add_portfolio_item', "Added portfolio item: $title");

        // Get the newly created item
        $stmt = $conn->prepare("SELECT * FROM portfolio_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        sendSuccessResponse($item, 'Portfolio item added successfully');
    } catch (PDOException $e) {
        sendErrorResponse('Database error: ' . $e->getMessage(), 500);
    }
}

// Update portfolio item
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        // Parse PUT request data
        parse_str(file_get_contents("php://input"), $putData);

        // Validate CSRF token
        if (!isset($putData['csrf_token']) || !validateCSRFToken($putData['csrf_token'])) {
            sendErrorResponse('Invalid CSRF token', 403);
        }

        // Validate required fields
        if (!isset($putData['id']) || !isset($putData['title']) || !isset($putData['category'])) {
            sendErrorResponse('ID, title, and category are required');
        }

        $id = (int)$putData['id'];
        $title = sanitizeInput($putData['title']);
        $description = sanitizeInput($putData['description'] ?? '');
        $category = sanitizeInput($putData['category']);

        $conn = getDBConnection();

        // Check if item exists
        $stmt = $conn->prepare("SELECT * FROM portfolio_items WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            sendErrorResponse('Portfolio item not found', 404);
        }

        // Handle image upload if new image is provided
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'portfolio/');
            if (!$uploadResult['success']) {
                sendErrorResponse($uploadResult['message']);
            }
            $imageUrl = $uploadResult['fileName'];
        }

        // Update database
        if ($imageUrl) {
            $stmt = $conn->prepare("UPDATE portfolio_items SET title = ?, description = ?, image_url = ?, category = ? WHERE id = ?");
            $stmt->execute([$title, $description, $imageUrl, $category, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE portfolio_items SET title = ?, description = ?, category = ? WHERE id = ?");
            $stmt->execute([$title, $description, $category, $id]);
        }

        // Log activity
        logActivity($_SESSION['user_id'], 'update_portfolio_item', "Updated portfolio item: $title");

        // Get the updated item
        $stmt = $conn->prepare("SELECT * FROM portfolio_items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        sendSuccessResponse($item, 'Portfolio item updated successfully');
    } catch (PDOException $e) {
        sendErrorResponse('Database error: ' . $e->getMessage(), 500);
    }
}

// Delete portfolio item
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        // Parse DELETE request data
        parse_str(file_get_contents("php://input"), $deleteData);

        // Validate CSRF token
        if (!isset($deleteData['csrf_token']) || !validateCSRFToken($deleteData['csrf_token'])) {
            sendErrorResponse('Invalid CSRF token', 403);
        }

        // Validate required fields
        if (!isset($deleteData['id'])) {
            sendErrorResponse('ID is required');
        }

        $id = (int)$deleteData['id'];

        $conn = getDBConnection();

        // Get item details before deletion
        $stmt = $conn->prepare("SELECT * FROM portfolio_items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            sendErrorResponse('Portfolio item not found', 404);
        }

        // Delete image file
        $imagePath = UPLOAD_PATH . 'portfolio/' . $item['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Delete from database
        $stmt = $conn->prepare("DELETE FROM portfolio_items WHERE id = ?");
        $stmt->execute([$id]);

        // Log activity
        logActivity($_SESSION['user_id'], 'delete_portfolio_item', "Deleted portfolio item: {$item['title']}");

        sendSuccessResponse(null, 'Portfolio item deleted successfully');
    } catch (PDOException $e) {
        sendErrorResponse('Database error: ' . $e->getMessage(), 500);
    }
}

// Handle invalid request
sendErrorResponse('Invalid request', 400);