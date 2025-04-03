<?php
require_once 'config.php';

try {
    // Generate new password hash
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Update admin password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hashedPassword]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Admin password reset successfully',
            'credentials' => [
                'username' => 'admin',
                'password' => 'admin123'
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Admin user not found'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} 