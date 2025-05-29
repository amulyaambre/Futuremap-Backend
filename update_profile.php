<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

$logFile = __DIR__ . '/debug.log';
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

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $input = json_decode(file_get_contents('php://input'), true);
    $requiredFields = ['email', 'phoneNumber', 'grade', 'stream', 'address'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || ($field !== 'address' && empty(trim($input[$field])))) {
            throw new Exception("Missing or empty $field", 400);
        }
    }

    $email = filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL);
    $phoneNumber = trim($input['phoneNumber']);
    $grade = trim($input['grade']);
    $stream = trim($input['stream']);
    $address = trim($input['address']);

    if (!$email) {
        throw new Exception('Invalid email format', 400);
    }
    if (!preg_match('/^\+?[1-9]\d{1,14}$/', $phoneNumber)) {
        throw new Exception('Invalid phone number format', 400);
    }
    if ($address && strlen($address) > 255) {
        throw new Exception('Address cannot exceed 255 characters', 400);
    }

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
        throw new Exception('Missing or invalid token', 401);
    }

    // Verify token and user
    $stmt = $pdo->prepare('SELECT email FROM users WHERE email = ? AND token = ?');
    $stmt->execute([$email, $token]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid token or user not found', 401);
    }

    // Verify grade and stream exist
    $stmt = $pdo->prepare("SELECT value FROM grade_stream_options WHERE type = 'grade' AND value = ?");
    $stmt->execute([$grade]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid grade selected', 400);
    }

    $stmt = $pdo->prepare("SELECT value FROM grade_stream_options WHERE type = 'stream' AND value = ?");
    $stmt->execute([$stream]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid stream selected', 400);
    }

    // Upsert user preferences
    $stmt = $pdo->prepare('
        INSERT INTO user_preferences (email, phone_number, grade, stream, address)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            phone_number = VALUES(phone_number),
            grade = VALUES(grade),
            stream = VALUES(stream),
            address = VALUES(address),
            updated_at = CURRENT_TIMESTAMP
    ');
    $stmt->execute([$email, $phoneNumber, $grade, $stream, $address]);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    file_put_contents(__DIR__ . '/error.log', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
}
?>