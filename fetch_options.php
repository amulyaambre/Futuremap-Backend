<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

$logFile = __DIR__ . '/debug.log';
$errorLog = __DIR__ . '/error.log';

file_put_contents($logFile, date('Y-m-d H:i:s') . " - Request: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Headers: " . json_encode(getallheaders()) . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $host = 'localhost';
    $dbname = 'career_platform';
    $username = 'root'; // Replace with your MySQL username
    $password = ''; // Replace with your MySQL password

    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Connecting to database...\n", FILE_APPEND);

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Extract token
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? trim($headers['Authorization']) : '';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - AuthHeader: $authHeader\n", FILE_APPEND);

    $token = '';
    if ($authHeader && preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
        $token = $matches[1];
    }
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Token: $token\n", FILE_APPEND);

    if (!$token) {
        new Exception('Missing or invalid token', 401);
    }

    // Verify token exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE token = ?');
    $stmt->execute([$token]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid token', 401);
    }

    // Fetch grades
    $stmt = $pdo->prepare("SELECT value FROM grade_stream_options WHERE type = 'grade'");
    $stmt->execute();
    $grades = $stmt->fetchAll(PDO::FETCH_COLUMN);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Grades: " . json_encode($grades) . "\n", FILE_APPEND);

    // Fetch streams
    $stmt = $pdo->prepare("SELECT value FROM grade_stream_options WHERE type = 'stream'");
    $stmt->execute();
    $streams = $stmt->fetchAll(PDO::FETCH_COLUMN);
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Streams: " . json_encode($streams) . "\n", FILE_APPEND);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'grades' => $grades,
        'streams' => $streams
    ]);
} catch (Exception $e) {
    $errorMessage = date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n";
    file_put_contents($errorLog, $errorMessage, FILE_APPEND);

    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>