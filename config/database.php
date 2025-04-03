<?php
/**
 * Database configuration file
 * 
 * This file contains the database connection settings for the application.
 */

/**
 * Get a PDO database connection
 * 
 * @return PDO|null A PDO database connection or null if connection fails
 */
function getConnection() {
    try {
        // Database connection parameters
        $host = 'localhost';
        $dbname = 'ainp';
        $username = 'root';
        $password = '';
        $charset = 'utf8mb4';
        
        // Create DSN (Data Source Name)
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        
        // PDO options
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        // Create PDO instance
        $pdo = new PDO($dsn, $username, $password, $options);
        
        return $pdo;
    } catch (PDOException $e) {
        // Log the error
        error_log("Database Connection Error: " . $e->getMessage());
        
        // Return null on failure
        return null;
    }
} 