<?php
// Include database configuration
require_once 'config.php';

// Set headers for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Test database connection
    $conn = getDbConnection();
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    $response['data']['connection'] = 'Database connection successful';

    // Default content items
    $defaultContent = [
        [
            'title' => 'Welcome to AIN VISIBILITY MEDIA',
            'content' => 'Your Partner in Digital Excellence',
            'section' => 'hero'
        ],
        [
            'title' => 'Digital Marketing',
            'content' => 'Strategic online marketing solutions including SEO, social media management, and content marketing to boost your online presence.',
            'section' => 'services'
        ],
        [
            'title' => 'Public Relations',
            'content' => 'Professional PR services to manage your brand reputation, media relations, and crisis communication.',
            'section' => 'services'
        ],
        [
            'title' => 'Branding',
            'content' => 'Comprehensive branding solutions including logo design, brand identity, and visual communication strategies.',
            'section' => 'services'
        ],
        [
            'title' => 'Analytics & Reporting',
            'content' => 'Data-driven insights and performance tracking to optimize your marketing campaigns and ROI.',
            'section' => 'services'
        ],
        [
            'title' => 'Contact Information',
            'content' => json_encode([
                'address' => 'Plot 27 Nasser Road, Zebra House RM. A12, P.O BOX 168869 Kampala',
                'phone' => '+256 701 521 524',
                'email' => 'ainvisibilitymedia@gmail.com'
            ]),
            'section' => 'contact'
        ]
    ];

    // Insert default content
    $stmt = $conn->prepare("INSERT INTO content (title, content, section, created_at, updated_at) VALUES (:title, :content, :section, NOW(), NOW())");
    
    $insertedContent = [];
    foreach ($defaultContent as $content) {
        try {
            $stmt->execute([
                ':title' => $content['title'],
                ':content' => $content['content'],
                ':section' => $content['section']
            ]);
            $insertedContent[] = [
                'id' => $conn->lastInsertId(),
                'title' => $content['title'],
                'section' => $content['section']
            ];
        } catch (PDOException $e) {
            // Skip if content already exists
            continue;
        }
    }

    $response['data']['inserted'] = $insertedContent;
    $response['success'] = true;
    $response['message'] = 'Default content inserted successfully';
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
exit;
?> 