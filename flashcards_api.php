<?php
// Set CORS headers for all responses
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database configuration
$host = 'localhost';
$dbname = 'career_platform';
$username = 'root'; // Replace with your MySQL username
$password = ''; // Replace with your MySQL password

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Get the Authorization header
$headers = getallheaders();
$authToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$authToken) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication token required']);
    exit;
}

// Verify the token by checking the users table
$stmt = $conn->prepare('SELECT id FROM users WHERE token = ?');
$stmt->execute([$authToken]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

$userId = $user['id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'getFlashcards') {
    try {
        // Fetch the user's education_level from user_profiles
        $stmt = $conn->prepare('SELECT education_level FROM user_profiles WHERE user_id = ?');
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$profile || !$profile['education_level']) {
            http_response_code(404);
            echo json_encode(['error' => 'User profile or education level not found']);
            exit;
        }

        $educationLevel = trim(strtolower($profile['education_level'])); // Normalize education_level

        // Debug: Log the education_level
        error_log("User ID: $userId, Education Level: $educationLevel");

        // Prepare SQL query to fetch flashcards matching the user's education_level
        $sql = "SELECT f.category, f.title, f.duration, f.cost, f.type 
                FROM flashcards f
                WHERE LOWER(f.education_level) = ?
                ORDER BY f.category, f.id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$educationLevel]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debug: Log the number of flashcards fetched
        error_log("Flashcards fetched: " . count($results));

        if (empty($results)) {
            echo json_encode(['error' => 'No flashcards found for education level: ' . $educationLevel]);
            exit;
        }

        // Define category-to-icon and category-to-color mappings
        $categoryConfig = [
            'courses' => ['icon' => 'GraduationCap', 'color' => 'blue'],
            'exams' => ['icon' => 'FileText', 'color' => 'red'],
            'skills' => ['icon' => 'Award', 'color' => 'green'],
            'institutions' => ['icon' => 'Building', 'color' => 'purple'],
            'default' => ['icon' => 'Briefcase', 'color' => 'orange']
        ];

        // Group by category
        $grouped = [];
        foreach ($results as $row) {
            $category = strtolower($row['category']);
            if (!isset($grouped[$category])) {
                $config = isset($categoryConfig[$category]) ? $categoryConfig[$category] : $categoryConfig['default'];
                $grouped[$category] = [
                    'icon' => $config['icon'],
                    'color' => $config['color'],
                    'items' => []
                ];
            }
            $grouped[$category]['items'][] = [
                'title' => $row['title'],
                'duration' => $row['duration'],
                'cost' => (int)$row['cost'],
                'type' => $row['type']
            ];
        }

        echo json_encode($grouped);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
}

$conn = null; // Close the connection
?>