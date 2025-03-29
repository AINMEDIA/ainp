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

// Get all services
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $conn = getDBConnection();
        $stmt = $conn->query("SELECT * FROM services ORDER BY created_at DESC");
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendSuccessResponse($services);
    } catch (PDOException $e) {
        sendErrorResponse('Database error: ' . $e->getMessage(), 500);
    }
}

// Add new service
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            sendErrorResponse('Invalid CSRF token', 403);
        }

        // Validate required fields
        if (!isset($_POST['title']) || !isset($_POST['description'])) {
            sendErrorResponse('Title and description are required');
        }

        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $icon = sanitizeInput($_POST['icon'] ?? '');

        // Insert into database
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO services (title, description, icon) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $icon]);

        $serviceId = $conn->lastInsertId();
        
        // Log activity
        logActivity($_SESSION['user_id'], 'add_service', "Added service: $title");

        // Get the newly created service
        $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        sendSuccessResponse($service, 'Service added successfully');
    } catch (PDOException $e) {
        sendErrorResponse('Database error: ' . $e->getMessage(), 500);
    }
}

// Update service
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        // Parse PUT request data
        parse_str(file_get_contents("php://input"), $putData);

        // Validate CSRF token
        if (!isset($putData['csrf_token']) || !validateCSRFToken($putData['csrf_token'])) {
            sendErrorResponse('Invalid CSRF token', 403);
        }

        // Validate required fields
        if (!isset($putData['id']) || !isset($putData['title']) || !isset($putData['description'])) {
            sendErrorResponse('ID, title, and description are required');
        }

        $id = (int)$putData['id'];
        $title = sanitizeInput($putData['title']);
        $description = sanitizeInput($putData['description']);
        $icon = sanitizeInput($putData['icon'] ?? '');

        $conn = getDBConnection();

        // Check if service exists
        $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            sendErrorResponse('Service not found', 404);
        }

        // Update database
        $stmt = $conn->prepare("UPDATE services SET title = ?, description = ?, icon = ? WHERE id = ?");
        $stmt->execute([$title, $description, $icon, $id]);

        // Log activity
        logActivity($_SESSION['user_id'], 'update_service', "Updated service: $title");

        // Get the updated service
        $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        sendSuccessResponse($service, 'Service updated successfully');
    } catch (PDOException $e) {
        sendErrorResponse('Database error: ' . $e->getMessage(), 500);
    }
}

// Delete service
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

        // Get service details before deletion
        $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$service) {
            sendErrorResponse('Service not found', 404);
        }

        // Delete from database
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);

        // Log activity
        logActivity($_SESSION['user_id'], 'delete_service', "Deleted service: {$service['title']}");

        sendSuccessResponse(null, 'Service deleted successfully');
    } catch (PDOException $e) {
        sendErrorResponse('Database error: ' . $e->getMessage(), 500);
    }
}

// Handle invalid request
sendErrorResponse('Invalid request', 400);