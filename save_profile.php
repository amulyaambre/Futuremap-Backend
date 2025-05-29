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
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Get the Authorization header
    $headers = getallheaders();
    $authToken = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

    if (!$authToken) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication token required']);
        exit;
    }

    // Verify the token by checking the users table
    $stmt = $pdo->prepare('SELECT id FROM users WHERE token = ?');
    $stmt->execute([$authToken]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        exit;
    }

    $userId = $user['id'];

    // Get the input data
    $input = json_decode(file_get_contents('php://input'), true);

    $requiredFields = ['educationLevel', 'scoreRange', 'interests', 'budget', 'timeline'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Missing $field"]);
            exit;
        }
    }

    $educationLevel = trim($input['educationLevel']);
    $scoreRange = trim($input['scoreRange']);
    $interests = json_encode($input['interests']); // Convert array to JSON string
    $budget = trim($input['budget']);
    $timeline = trim($input['timeline']);

    // Check if profile exists for the user
    $stmt = $pdo->prepare('SELECT id FROM user_profiles WHERE user_id = ?');
    $stmt->execute([$userId]);
    $existingProfile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingProfile) {
        // Update existing profile
        $stmt = $pdo->prepare(
            'UPDATE user_profiles 
             SET education_level = ?, score_range = ?, interests = ?, budget = ?, timeline = ?, updated_at = CURRENT_TIMESTAMP 
             WHERE user_id = ?'
        );
        $stmt->execute([$educationLevel, $scoreRange, $interests, $budget, $timeline, $userId]);
    } else {
        // Insert new profile
        $stmt = $pdo->prepare(
            'INSERT INTO user_profiles (user_id, education_level, score_range, interests, budget, timeline) 
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $educationLevel, $scoreRange, $interests, $budget, $timeline]);
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Profile saved successfully']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>