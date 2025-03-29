<?php
require_once '../config/config.php';

header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    exit(0);
}

// Require login for all operations
requireLogin();

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Get file details or list all files
            if (isset($_GET['id'])) {
                $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $file = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$file) {
                    throw new Exception('File not found');
                }
                
                // If action is download, serve the file
                if (isset($_GET['action']) && $_GET['action'] === 'download') {
                    if (file_exists($file['filepath'])) {
                        header('Content-Type: ' . $file['filetype']);
                        header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
                        header('Content-Length: ' . filesize($file['filepath']));
                        readfile($file['filepath']);
                        exit;
                    } else {
                        throw new Exception('File not found on server');
                    }
                }
                
                // For preview, get file content if it's a text file
                if (in_array($file['filetype'], ['text/plain', 'text/html', 'text/css', 'application/javascript'])) {
                    $file['content'] = file_get_contents($file['filepath']);
                }
                
                sendSuccessResponse($file);
            } else {
                // List all files
                $stmt = $pdo->query("SELECT * FROM files ORDER BY uploaded_at DESC");
                $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendSuccessResponse($files);
            }
            break;
            
        case 'POST':
            // Create new folder
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['name']) || !isset($data['type']) || $data['type'] !== 'folder') {
                throw new Exception('Invalid request');
            }
            
            $folderPath = UPLOAD_PATH . '/' . sanitizeInput($data['name']);
            
            if (file_exists($folderPath)) {
                throw new Exception('Folder already exists');
            }
            
            if (!mkdir($folderPath, 0755, true)) {
                throw new Exception('Failed to create folder');
            }
            
            // Log activity
            logActivity($_SESSION['user_id'], 'Created folder: ' . $data['name']);
            
            sendSuccessResponse(['path' => $folderPath]);
            break;
            
        case 'DELETE':
            // Delete file
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                throw new Exception('File ID is required');
            }
            
            $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
            $stmt->execute([$data['id']]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$file) {
                throw new Exception('File not found');
            }
            
            // Delete file from server
            if (file_exists($file['filepath'])) {
                unlink($file['filepath']);
            }
            
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
            $stmt->execute([$data['id']]);
            
            // Log activity
            logActivity($_SESSION['user_id'], 'Deleted file: ' . $file['filename']);
            
            sendSuccessResponse(['message' => 'File deleted successfully']);
            break;
            
        default:
            throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    sendErrorResponse($e->getMessage());
}