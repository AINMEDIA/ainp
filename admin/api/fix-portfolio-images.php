<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $conn = getDbConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Get all portfolio items
    $stmt = $conn->query("SELECT id, image_url FROM portfolio");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $fixedItems = [];
    $missingItems = [];
    
    foreach ($items as $item) {
        $imageUrl = $item['image_url'];
        
        // Fix double /ainp/ prefix
        if (strpos($imageUrl, '/ainp//ainp/') === 0) {
            $imageUrl = str_replace('/ainp//ainp/', '/ainp/', $imageUrl);
            
            // Update the item with the fixed URL
            $updateStmt = $conn->prepare("UPDATE portfolio SET image_url = ? WHERE id = ?");
            $updateStmt->execute([$imageUrl, $item['id']]);
            
            $fixedItems[] = [
                'id' => $item['id'],
                'old_url' => $item['image_url'],
                'new_url' => $imageUrl,
                'fix_type' => 'double_prefix'
            ];
        }
        
        // Check if the image exists
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $imageUrl;
        if (!file_exists($filePath)) {
            $missingItems[] = $item;
            
            // Update the item to use a default image
            $defaultImage = '/ainp/uploads/default-portfolio.jpg';
            $updateStmt = $conn->prepare("UPDATE portfolio SET image_url = ? WHERE id = ?");
            $updateStmt->execute([$defaultImage, $item['id']]);
            
            $fixedItems[] = [
                'id' => $item['id'],
                'old_url' => $imageUrl,
                'new_url' => $defaultImage,
                'fix_type' => 'missing_file'
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => count($fixedItems) . ' portfolio items fixed',
        'fixed_items' => $fixedItems,
        'missing_items' => $missingItems
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 