<?php
require_once 'config.php';

// Get hero section content
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $conn->prepare("SELECT * FROM hero_section LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            send_response('success', 'Hero section content retrieved', $result);
        } else {
            send_response('error', 'No hero section content found');
        }
    } catch(PDOException $e) {
        send_response('error', 'Database error: ' . $e->getMessage());
    }
}

// Update hero section content
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        send_response('error', 'Invalid input data');
    }
    
    $title = sanitize_input($data['title']);
    $subtitle = sanitize_input($data['subtitle']);
    $video_url = sanitize_input($data['video_url']);
    
    try {
        $stmt = $conn->prepare("UPDATE hero_section SET title = ?, subtitle = ?, video_url = ?, updated_at = NOW() WHERE id = 1");
        $result = $stmt->execute([$title, $subtitle, $video_url]);
        
        if ($result) {
            send_response('success', 'Hero section updated successfully');
        } else {
            send_response('error', 'Failed to update hero section');
        }
    } catch(PDOException $e) {
        send_response('error', 'Database error: ' . $e->getMessage());
    }
}
?> 