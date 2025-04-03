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
        // Get all users or search users
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        
        try {
            if (!empty($searchTerm)) {
                $stmt = $conn->prepare("SELECT id, name, email, role, status, created_at FROM users WHERE name LIKE :search OR email LIKE :search");
                $searchTerm = "%$searchTerm%";
                $stmt->bindParam(':search', $searchTerm);
            } else {
                $stmt = $conn->prepare("SELECT id, name, email, role, status, created_at FROM users");
            }
            
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            sendJsonResponse(['success' => true, 'users' => $users]);
        } catch (PDOException $e) {
            sendJsonResponse(['success' => false, 'message' => 'Error fetching users: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'POST':
        // Create a new user
        $data = getJsonRequestBody();
        
        // Validate required fields
        if (!isset($data['name']) || !isset($data['email']) || !isset($data['role']) || !isset($data['password'])) {
            sendJsonResponse(['success' => false, 'message' => 'Missing required fields'], 400);
        }
        
        try {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $data['email']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                sendJsonResponse(['success' => false, 'message' => 'Email already exists'], 400);
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, role, password, status, created_at) VALUES (:name, :email, :role, :password, :status, NOW())");
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':password', $hashedPassword);
            
            // Set default status
            $status = 'active';
            $stmt->bindParam(':status', $status);
            
            $stmt->execute();
            
            // Get the new user ID
            $userId = $conn->lastInsertId();
            
            sendJsonResponse(['success' => true, 'message' => 'User created successfully', 'userId' => $userId]);
        } catch (PDOException $e) {
            sendJsonResponse(['success' => false, 'message' => 'Error creating user: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'PUT':
        // Update an existing user
        $data = getJsonRequestBody();
        
        // Validate required fields
        if (!isset($data['id'])) {
            sendJsonResponse(['success' => false, 'message' => 'User ID is required'], 400);
        }
        
        try {
            // Build update query dynamically based on provided fields
            $updateFields = [];
            $params = [];
            
            if (isset($data['name'])) {
                $updateFields[] = "name = :name";
                $params[':name'] = $data['name'];
            }
            
            if (isset($data['email'])) {
                $updateFields[] = "email = :email";
                $params[':email'] = $data['email'];
            }
            
            if (isset($data['role'])) {
                $updateFields[] = "role = :role";
                $params[':role'] = $data['role'];
            }
            
            if (isset($data['password'])) {
                $updateFields[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (isset($data['status'])) {
                $updateFields[] = "status = :status";
                $params[':status'] = $data['status'];
            }
            
            if (empty($updateFields)) {
                sendJsonResponse(['success' => false, 'message' => 'No fields to update'], 400);
            }
            
            // Add ID to params
            $params[':id'] = $data['id'];
            
            // Build and execute query
            $query = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            
            sendJsonResponse(['success' => true, 'message' => 'User updated successfully']);
        } catch (PDOException $e) {
            sendJsonResponse(['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'DELETE':
        // Delete a user
        $userId = isset($_GET['id']) ? $_GET['id'] : null;
        
        if (!$userId) {
            sendJsonResponse(['success' => false, 'message' => 'User ID is required'], 400);
        }
        
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            sendJsonResponse(['success' => true, 'message' => 'User deleted successfully']);
        } catch (PDOException $e) {
            sendJsonResponse(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()], 500);
        }
        break;
        
    default:
        sendJsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        break;
}
?>