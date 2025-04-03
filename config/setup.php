<?php
// Start output buffering
ob_start();

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log messages
function logMessage($message) {
    echo $message . "<br>";
}

try {
    // Create uploads directory if it doesn't exist
    $uploads_dir = __DIR__ . '/../uploads';
    if (!file_exists($uploads_dir)) {
        if (mkdir($uploads_dir, 0777, true)) {
            logMessage("Created uploads directory at: " . $uploads_dir);
        } else {
            throw new Exception("Failed to create uploads directory");
        }
    }

    // Connect to MySQL without database
    $pdo = new PDO(
        "mysql:host=localhost",
        "root",
        "",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/setup.sql');
    $pdo->exec($sql);
    logMessage("Successfully executed SQL setup script");

    // Test connection to the new database
    $pdo = new PDO(
        "mysql:host=localhost;dbname=ainp",
        "root",
        "",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
    logMessage("Successfully connected to the ainp database");

    // Create a default portfolio image if it doesn't exist
    $default_image = $uploads_dir . '/default-portfolio.jpg';
    if (!file_exists($default_image)) {
        // Create a simple colored image
        $image = imagecreatetruecolor(800, 600);
        $bg_color = imagecolorallocate($image, 240, 240, 240);
        imagefill($image, 0, 0, $bg_color);
        
        // Add some text
        $text_color = imagecolorallocate($image, 100, 100, 100);
        $text = "Default Portfolio Image";
        $font_size = 5;
        $text_width = imagefontwidth($font_size) * strlen($text);
        $text_height = imagefontheight($font_size);
        $x = (800 - $text_width) / 2;
        $y = (600 - $text_height) / 2;
        imagestring($image, $font_size, $x, $y, $text, $text_color);
        
        // Save the image
        imagejpeg($image, $default_image);
        imagedestroy($image);
        logMessage("Created default portfolio image");
    }

    logMessage("<br>Setup completed successfully! You can now use the fix-urls tool.");

} catch (Exception $e) {
    logMessage("Error: " . $e->getMessage());
}

// End output buffering
ob_end_flush(); 