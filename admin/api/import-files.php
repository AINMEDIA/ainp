<?php
// Start output buffering
ob_start();

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to HTML
header('Content-Type: text/html; charset=utf-8');

// Function to log messages
function logMessage($message) {
    echo $message . "<br>";
}

try {
    // Include database connection
    require_once '../../config/database.php';
    
    // Get database connection
    $pdo = getConnection();
    if (!$pdo) {
        throw new Exception("Failed to connect to database");
    }
    
    logMessage("<h2>Import Files from Uploads Directory</h2>");
    
    // Check if uploads directory exists
    $uploads_dir = '../../uploads';
    if (!file_exists($uploads_dir)) {
        throw new Exception("Uploads directory not found at: " . realpath($uploads_dir));
    }
    
    // Get all files from uploads directory
    $files = glob($uploads_dir . '/*');
    if (empty($files)) {
        throw new Exception("No files found in uploads directory");
    }
    
    logMessage("Found " . count($files) . " files in uploads directory");
    
    // Import files to media table
    $media_count = 0;
    $portfolio_count = 0;
    
    foreach ($files as $file) {
        $filename = basename($file);
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Determine file type
        $type = 'image';
        if (in_array($file_ext, ['mp4', 'webm', 'mov'])) {
            $type = 'video';
        } elseif (in_array($file_ext, ['mp3', 'wav', 'ogg'])) {
            $type = 'audio';
        } elseif (in_array($file_ext, ['pdf', 'doc', 'docx', 'txt'])) {
            $type = 'document';
        }
        
        // Check if file already exists in media table
        $stmt = $pdo->prepare("SELECT id FROM media WHERE url = ?");
        $stmt->execute(['/ainp/uploads/' . $filename]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existing) {
            // Insert into media table
            $stmt = $pdo->prepare("INSERT INTO media (url, type, title, description, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $title = ucfirst(pathinfo($filename, PATHINFO_FILENAME));
            $stmt->execute(['/ainp/uploads/' . $filename, $type, $title, 'Imported from uploads directory']);
            $media_count++;
            
            // If it's an image, also add to portfolio table
            if ($type === 'image') {
                $stmt = $pdo->prepare("INSERT INTO portfolio (title, description, image_url, category, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                $stmt->execute([$title, 'Imported from uploads directory', '/ainp/uploads/' . $filename, 'Imported']);
                $portfolio_count++;
            }
        }
    }
    
    logMessage("<h3>Import Results</h3>");
    logMessage("Added $media_count new items to media table");
    logMessage("Added $portfolio_count new items to portfolio table");
    
    logMessage("<h3>Next Steps</h3>");
    logMessage("<p>Files have been imported into your database. You can now:</p>");
    logMessage("<ol>");
    logMessage("<li><a href='../fix-urls.html'>Run the Fix URLs tool</a> to ensure all URLs are correct</li>");
    logMessage("<li><a href='check-database.php'>Check the database</a> to see the imported items</li>");
    logMessage("<li>Edit the portfolio items to add more details</li>");
    logMessage("</ol>");
    
} catch (Exception $e) {
    logMessage("<h3>Error</h3>");
    logMessage("Error: " . $e->getMessage());
}

// End output buffering
ob_end_flush(); 