<?php
require_once 'config.php';

// Add debugging
error_log("Auth request received: " . $_SERVER['REQUEST_METHOD']);
error_log("Request headers: " . print_r(getallheaders(), true));

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = file_get_contents('php://input');
error_log("Received input: " . $input);

$data = json_decode($input, true);
error_log("Decoded data: " . print_r($data, true));

if (!isset($data['username']) || !isset($data['password'])) {
    error_log("Missing credentials");
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing credentials']);
    exit;
}

try {
    // Log the SQL query
    error_log("Attempting to find user: " . $data['username']);
    
    $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    error_log("User found: " . ($user ? "Yes" : "No"));
    if ($user) {
        error_log("User data: " . print_r($user, true));
    }

    if ($user && password_verify($data['password'], $user['password'])) {
        error_log("Password verification successful");
        // Start session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;

        // Generate CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        error_log("Login successful for user: " . $user['username']);
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ],
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    } else {
        error_log("Invalid credentials for user: " . $data['username']);
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
} 