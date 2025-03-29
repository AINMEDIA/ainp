<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$db_config = [
    'host' => 'localhost',
    'dbname' => 'ainp_db',
    'username' => 'root',
    'password' => ''
];

try {
    $pdo = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset=utf8mb4",
        $db_config['username'],
        $db_config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Get single user
            $stmt = $pdo->prepare('SELECT id, name, email, role, status FROM users WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $user = $stmt->fetch();
            
            if ($user) {
                echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
        } else {
            // Get all users
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $query = 'SELECT id, name, email, role, status FROM users';
            
            if ($search) {
                $query .= ' WHERE name LIKE ? OR email LIKE ?';
                $search = "%$search%";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$search, $search]);
            } else {
                $stmt = $pdo->query($query);
            }
            
            $users = $stmt->fetchAll();
            echo json_encode($users);
        }
        break;

    case 'POST':
        // Create new user
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }

        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $pdo->prepare('
            INSERT INTO users (name, email, password, role, status)
            VALUES (?, ?, ?, ?, ?)
        ');
        
        try {
            $stmt->execute([
                $data['name'],
                $data['email'],
                $hashedPassword,
                $data['role'] ?? 'user',
                $data['status'] ?? 'active'
            ]);
            
            $userId = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully',
                'id' => $userId
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error creating user']);
        }
        break;

    case 'PUT':
        // Update user
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $_GET['id'] ?? null;

        if (!$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit;
        }

        // Validate required fields
        if (!isset($data['name']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        // Check if email exists for other users
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$data['email'], $userId]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }

        // Prepare update query
        $query = 'UPDATE users SET name = ?, email = ?, role = ?, status = ?';
        $params = [$data['name'], $data['email'], $data['role'], $data['status']];

        // Update password if provided
        if (!empty($data['password'])) {
            $query .= ', password = ?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $query .= ' WHERE id = ?';
        $params[] = $userId;

        $stmt = $pdo->prepare($query);

        try {
            $stmt->execute($params);
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error updating user']);
        }
        break;

    case 'DELETE':
        // Delete user
        $userId = $_GET['id'] ?? null;

        if (!$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit;
        }

        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');

        try {
            $stmt->execute([$userId]);
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting user']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}