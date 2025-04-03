<?php
require_once 'config.php';

try {
    // Check database connection
    echo "Database connection successful\n";
    
    // Check users table
    $stmt = $conn->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "Users table exists\n";
        
        // Check admin user
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = 'admin'");
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "Admin user found:\n";
            echo "ID: " . $user['id'] . "\n";
            echo "Username: " . $user['username'] . "\n";
            echo "Email: " . $user['email'] . "\n";
            echo "Role: " . $user['role'] . "\n";
            echo "Password hash: " . $user['password'] . "\n";
            
            // Test password verification
            $testPassword = 'admin123';
            if (password_verify($testPassword, $user['password'])) {
                echo "Password verification successful\n";
            } else {
                echo "Password verification failed\n";
            }
        } else {
            echo "Admin user not found\n";
        }
    } else {
        echo "Users table does not exist\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 