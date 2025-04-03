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
    
    logMessage("<h2>Database Check Results</h2>");
    
    // Check portfolio table
    logMessage("<h3>Portfolio Table</h3>");
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM portfolio");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    logMessage("Total items: " . $result['count']);
    
    if ($result['count'] > 0) {
        $stmt = $pdo->query("SELECT id, title, image_url FROM portfolio LIMIT 5");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        logMessage("<table border='1' cellpadding='5'>");
        logMessage("<tr><th>ID</th><th>Title</th><th>Image URL</th></tr>");
        foreach ($items as $item) {
            logMessage("<tr>");
            logMessage("<td>" . htmlspecialchars($item['id']) . "</td>");
            logMessage("<td>" . htmlspecialchars($item['title']) . "</td>");
            logMessage("<td>" . htmlspecialchars($item['image_url']) . "</td>");
            logMessage("</tr>");
        }
        logMessage("</table>");
        
        if ($result['count'] > 5) {
            logMessage("... and " . ($result['count'] - 5) . " more items");
        }
    }
    
    // Check media table
    logMessage("<h3>Media Table</h3>");
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM media");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    logMessage("Total items: " . $result['count']);
    
    if ($result['count'] > 0) {
        $stmt = $pdo->query("SELECT id, title, url, type FROM media LIMIT 5");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        logMessage("<table border='1' cellpadding='5'>");
        logMessage("<tr><th>ID</th><th>Title</th><th>URL</th><th>Type</th></tr>");
        foreach ($items as $item) {
            logMessage("<tr>");
            logMessage("<td>" . htmlspecialchars($item['id']) . "</td>");
            logMessage("<td>" . htmlspecialchars($item['title']) . "</td>");
            logMessage("<td>" . htmlspecialchars($item['url']) . "</td>");
            logMessage("<td>" . htmlspecialchars($item['type']) . "</td>");
            logMessage("</tr>");
        }
        logMessage("</table>");
        
        if ($result['count'] > 5) {
            logMessage("... and " . ($result['count'] - 5) . " more items");
        }
    }
    
    // Check uploads directory
    $uploads_dir = '../../uploads';
    logMessage("<h3>Uploads Directory</h3>");
    if (file_exists($uploads_dir)) {
        logMessage("Directory exists: Yes");
        logMessage("Directory path: " . realpath($uploads_dir));
        
        // Count files
        $files = glob($uploads_dir . '/*');
        logMessage("Number of files: " . count($files));
        
        // List first 5 files
        if (count($files) > 0) {
            logMessage("<h4>Sample Files:</h4>");
            logMessage("<ul>");
            for ($i = 0; $i < min(5, count($files)); $i++) {
                $filename = basename($files[$i]);
                logMessage("<li>" . htmlspecialchars($filename) . "</li>");
            }
            logMessage("</ul>");
            
            if (count($files) > 5) {
                logMessage("... and " . (count($files) - 5) . " more files");
            }
        }
    } else {
        logMessage("Directory exists: No");
    }
    
    logMessage("<h3>Conclusion</h3>");
    if ($result['count'] == 0) {
        logMessage("The database tables are empty. You need to add some portfolio and media items first.");
    } else {
        logMessage("The database contains items. If the fix-urls tool didn't fix anything, it means all URLs are already correct.");
    }
    
} catch (Exception $e) {
    logMessage("<h3>Error</h3>");
    logMessage("Error: " . $e->getMessage());
}

// End output buffering
ob_end_flush(); 