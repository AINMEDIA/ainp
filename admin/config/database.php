<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ainp_db');

// Create database connection
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to generate response
function send_response($status, $message, $data = null) {
    $response = array(
        'status' => $status,
        'message' => $message,
        'data' => $data
    );
    echo json_encode($response);
    exit();
}

// Function to check if user is logged in
function is_logged_in() {
    session_start();
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Function to require login
function require_login() {
    if (!is_logged_in()) {
        header('Location: /admin/login.html');
        exit();
    }
}

// Function to validate CSRF token
function validate_csrf_token($token) {
    session_start();
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}

// Function to log activity
function log_activity($user_id, $action, $details = '') {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $action, $details]);
    } catch(PDOException $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}

// Function to get user data
function get_user_data($user_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Error getting user data: " . $e->getMessage());
        return false;
    }
}

// Function to update user data
function update_user_data($user_id, $data) {
    global $conn;
    try {
        $allowed_fields = ['username', 'email', 'role'];
        $updates = [];
        $params = [];

        foreach ($data as $field => $value) {
            if (in_array($field, $allowed_fields)) {
                $updates[] = "$field = ?";
                $params[] = $value;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $params[] = $user_id;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    } catch(PDOException $e) {
        error_log("Error updating user data: " . $e->getMessage());
        return false;
    }
}

// Create tables if they don't exist
function initializeDatabase() {
    global $conn;
    
    // Users table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        full_name VARCHAR(100),
        profile_pic VARCHAR(255),
        role ENUM('admin', 'editor', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Files table
    $conn->exec("CREATE TABLE IF NOT EXISTS files (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        filename VARCHAR(255) NOT NULL,
        filepath VARCHAR(255) NOT NULL,
        filetype VARCHAR(50),
        filesize INT,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // User permissions table
    $conn->exec("CREATE TABLE IF NOT EXISTS user_permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        permission VARCHAR(50) NOT NULL,
        granted_by INT,
        granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (granted_by) REFERENCES users(id)
    )");

    // Create default admin if not exists
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $defaultAdmin = [
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'email' => 'admin@example.com',
            'full_name' => 'System Administrator',
            'role' => 'admin'
        ];
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $defaultAdmin['username'],
            $defaultAdmin['password'],
            $defaultAdmin['email'],
            $defaultAdmin['full_name'],
            $defaultAdmin['role']
        ]);
    }
}

// Initialize database
initializeDatabase();