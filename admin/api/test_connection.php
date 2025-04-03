<?php
// Include database configuration
require_once 'config.php';

// Set headers for API
header('Content-Type: application/json');

// Test database connection
try {
    $conn = getDbConnection();
    
    if ($conn) {
        // Test query to check if tables exist
        $tables = ['users', 'content', 'media', 'settings'];
        $existingTables = [];
        
        foreach ($tables as $table) {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $existingTables[] = $table;
            }
        }
        
        // Check if admin user exists
        $stmt = $conn->query("SELECT * FROM users WHERE email = 'ainvisibilitymedia@gmail.com'");
        $adminExists = $stmt->rowCount() > 0;
        
        echo json_encode([
            'success' => true,
            'message' => 'Database connection successful',
            'database' => DB_NAME,
            'existing_tables' => $existingTables,
            'admin_exists' => $adminExists
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 