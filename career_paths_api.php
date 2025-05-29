<?php
// Set CORS headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: POST, OPTIONS');
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

// Verify the token
$stmt = $conn->prepare('SELECT id FROM users WHERE token = ?');
$stmt->execute([$authToken]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

$userId = $user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the raw POST data
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            exit;
        }

        $pathId = isset($input['pathId']) ? trim($input['pathId']) : null;
        $cards = isset($input['cards']) ? $input['cards'] : [];
        $connections = isset($input['connections']) ? $input['connections'] : [];

        if (!$pathId) {
            http_response_code(400);
            echo json_encode(['error' => 'Path ID is required']);
            exit;
        }

        // Validate cards and connections
        if (!is_array($cards) || !is_array($connections)) {
            http_response_code(400);
            echo json_encode(['error' => 'Cards and connections must be arrays']);
            exit;
        }

        // Prepare SQL to insert or update the career path
        $sql = "INSERT INTO career_paths (user_id, path_id, cards, connections)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                cards = VALUES(cards),
                connections = VALUES(connections),
                updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $userId,
            $pathId,
            json_encode($cards),
            json_encode($connections)
        ]);

        http_response_code(200);
        echo json_encode(['message' => 'Career path saved successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

$conn = null;
?>