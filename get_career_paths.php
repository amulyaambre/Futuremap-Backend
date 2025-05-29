<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = 'localhost';
$dbname = 'career_platform';
$username = 'root'; // Replace with your MySQL username
$password = ''; // Replace with your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['user_id']) || !is_numeric($input['user_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or missing user_id']);
        exit;
    }

    $user_id = $input['user_id'];
    $stmt = $pdo->prepare('SELECT id, user_id, path_id, cards, connections, created_at, updated_at FROM career_paths WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $paths = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Parse JSON in cards field
    foreach ($paths as &$path) {
        $path['cards'] = json_decode($path['cards'], true) ?? [];
        $path['card_count'] = count($path['cards']);
        // Mock progress for demonstration (replace with actual logic)
        $path['progress'] = rand(10, 90);
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $paths
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>