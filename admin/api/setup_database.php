<?php
// Include database configuration
require_once 'config.php';

// Set headers for API
header('Content-Type: application/json');

try {
    // Create database connection without selecting a database
    $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $conn->exec("USE " . DB_NAME);
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // Split SQL by semicolon to execute each statement separately
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $results = [];
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $conn->exec($statement);
            $results[] = [
                'success' => true,
                'statement' => substr($statement, 0, 50) . '...'
            ];
        } catch (PDOException $e) {
            // Skip "already exists" errors
            if ($e->getCode() != '42S01') {  // Table already exists
                $results[] = [
                    'success' => false,
                    'statement' => substr($statement, 0, 50) . '...',
                    'error' => $e->getMessage()
                ];
            }
        }
    }
    
    // Verify tables exist
    $tables = ['users', 'content', 'media', 'settings'];
    $existingTables = [];
    
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $existingTables[] = $table;
        }
    }
    
    // Check if settings exist
    $stmt = $conn->query("SELECT COUNT(*) FROM settings");
    $settingsCount = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'message' => 'Database setup completed',
        'database' => DB_NAME,
        'existing_tables' => $existingTables,
        'settings_count' => $settingsCount,
        'results' => $results
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database setup failed: ' . $e->getMessage()
    ]);
}
?> 