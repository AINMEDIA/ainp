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

    // Get all media items
    $stmt = $conn->query("SELECT id, url FROM media");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $fixedItems = [];
    
    foreach ($items as $item) {
        $url = $item['url'];
        $originalUrl = $url;
        
        // Fix double /ainp/ prefix
        if (strpos($url, '/ainp//ainp/') === 0) {
            $url = str_replace('/ainp//ainp/', '/ainp/', $url);
            
            // Update the item with the fixed URL
            $updateStmt = $conn->prepare("UPDATE media SET url = ? WHERE id = ?");
            $updateStmt->execute([$url, $item['id']]);
            
            $fixedItems[] = [
                'id' => $item['id'],
                'old_url' => $originalUrl,
                'new_url' => $url,
                'fix_type' => 'double_prefix'
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => count($fixedItems) . ' media items fixed',
        'fixed_items' => $fixedItems
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 