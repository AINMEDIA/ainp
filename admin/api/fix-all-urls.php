<?php
// Start output buffering
ob_start();

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON content type
header('Content-Type: application/json');

// Function to log errors
function logError($message) {
    error_log("Fix URLs Error: " . $message);
}

try {
    // Check if database.php exists
    if (!file_exists('../../config/database.php')) {
        throw new Exception("Database configuration file not found. Please create the file at: " . realpath('../../') . "/config/database.php");
    }
    
    // Include database connection
    require_once '../../config/database.php';
    
    // Check if getConnection function exists
    if (!function_exists('getConnection')) {
        throw new Exception("Database connection function not found in database.php");
    }
    
    // Get database connection
    $pdo = getConnection();
    if (!$pdo) {
        throw new Exception("Failed to connect to database. Please check your database credentials and make sure the database exists.");
    }
    
    // Check if tables exist
    $tables = ['portfolio', 'media'];
    $missing_tables = [];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() === 0) {
            $missing_tables[] = $table;
        }
    }
    
    if (!empty($missing_tables)) {
        throw new Exception("The following tables are missing: " . implode(', ', $missing_tables) . ". Please create these tables first.");
    }
    
    $fixed_items = [];
    $total_fixed = 0;

    // Fix portfolio items
    try {
        $stmt = $pdo->query("SELECT id, image_url FROM portfolio");
        $portfolio_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($portfolio_items as $item) {
            $old_url = $item['image_url'];
            $new_url = $old_url;

            // Fix double /ainp/ prefix
            if (strpos($old_url, '/ainp//ainp/') === 0) {
                $new_url = str_replace('/ainp//ainp/', '/ainp/', $old_url);
            }
            // Add /ainp/ prefix if missing
            else if (strpos($old_url, '/ainp/') !== 0) {
                $new_url = '/ainp/' . $old_url;
            }

            if ($old_url !== $new_url) {
                $update_stmt = $pdo->prepare("UPDATE portfolio SET image_url = ? WHERE id = ?");
                $update_stmt->execute([$new_url, $item['id']]);
                $fixed_items[] = [
                    'table' => 'portfolio',
                    'id' => $item['id'],
                    'old_url' => $old_url,
                    'new_url' => $new_url
                ];
                $total_fixed++;
            }
        }
    } catch (PDOException $e) {
        logError("Portfolio table error: " . $e->getMessage());
        // Continue with other operations
    }

    // Fix media items
    try {
        $stmt = $pdo->query("SELECT id, url FROM media");
        $media_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($media_items as $item) {
            $old_url = $item['url'];
            $new_url = $old_url;

            // Fix double /ainp/ prefix
            if (strpos($old_url, '/ainp//ainp/') === 0) {
                $new_url = str_replace('/ainp//ainp/', '/ainp/', $old_url);
            }
            // Add /ainp/ prefix if missing
            else if (strpos($old_url, '/ainp/') !== 0) {
                $new_url = '/ainp/' . $old_url;
            }

            if ($old_url !== $new_url) {
                $update_stmt = $pdo->prepare("UPDATE media SET url = ? WHERE id = ?");
                $update_stmt->execute([$new_url, $item['id']]);
                $fixed_items[] = [
                    'table' => 'media',
                    'id' => $item['id'],
                    'old_url' => $old_url,
                    'new_url' => $new_url
                ];
                $total_fixed++;
            }
        }
    } catch (PDOException $e) {
        logError("Media table error: " . $e->getMessage());
        // Continue with other operations
    }

    // Check if default portfolio image exists
    try {
        $default_image_path = '../../uploads/default-portfolio.jpg';
        if (!file_exists($default_image_path)) {
            // Copy an existing image as default
            $stmt = $pdo->query("SELECT url FROM media WHERE type = 'image' LIMIT 1");
            $default_image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($default_image) {
                $source_path = '../../' . str_replace('/ainp/', '', $default_image['url']);
                if (file_exists($source_path)) {
                    copy($source_path, $default_image_path);
                    $fixed_items[] = [
                        'action' => 'created_default_image',
                        'source' => $default_image['url'],
                        'destination' => '/ainp/uploads/default-portfolio.jpg'
                    ];
                }
            }
        }
    } catch (Exception $e) {
        logError("Default image creation error: " . $e->getMessage());
        // Continue with other operations
    }

    // Clear any output buffers
    ob_clean();
    
    // Output JSON response
    $response = [
        'success' => true,
        'message' => "Successfully fixed $total_fixed items",
        'fixed_items' => $fixed_items
    ];
    
    echo json_encode($response);

} catch (Exception $e) {
    // Log the error
    logError($e->getMessage());
    
    // Clear any output buffers
    ob_clean();
    
    // Output error JSON
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

// End output buffering
ob_end_flush();
?> 