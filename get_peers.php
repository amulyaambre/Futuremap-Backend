<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
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

    // Validate token
    $token = isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : '';
    if (!$token) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Missing authorization token']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE token = ? AND id = ?');
    $stmt->execute([$token, $input['user_id']]);
    if (!$stmt->fetch()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit;
    }

    $user_id = $input['user_id'];
    // Get current user's interests
    $stmt = $pdo->prepare('SELECT interests FROM user_profiles WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $user_profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_profile) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User profile not found']);
        exit;
    }

    $user_interests = json_decode($user_profile['interests'], true) ?? [];
    if (empty($user_interests)) {
        http_response_code(200);
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    // Fetch other users' profiles
    $stmt = $pdo->prepare('SELECT up.user_id, up.interests, u.first_name, up.education_level FROM user_profiles up JOIN users u ON up.user_id = u.id WHERE up.user_id != ?');
    $stmt->execute([$user_id]);
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $peers = [];
    foreach ($profiles as $profile) {
        $peer_interests = json_decode($profile['interests'], true) ?? [];
        $common_interests = array_intersect($user_interests, $peer_interests);
        $similarity = count($common_interests) / max(count($user_interests), count($peer_interests)) * 100;

        if ($similarity > 50) { // Threshold for similarity
            $peers[] = [
                'name' => $profile['first_name'],
                'path' => implode(', ', $peer_interests), // Display interests as path
                'institution' => $profile['education_level'],
                'similarity' => round($similarity)
            ];
        }
    }

    // Sort by similarity descending
    usort($peers, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
    $peers = array_slice($peers, 0, 3); // Limit to top 3

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $peers
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>