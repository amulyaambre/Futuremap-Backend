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
    $stmt = $pdo->prepare('
        SELECT s.id, s.user_id, s.mentor_id, s.session_time, s.status, m.name, m.specialization, m.rating, m.sessions
        FROM session_bookings s
        JOIN mentors m ON s.mentor_id = m.id
        WHERE s.user_id = ?
    ');
    $stmt->execute([$user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mentors = array_map(function($booking) {
        return [
            'name' => $booking['name'],
            'expertise' => $booking['specialization'],
            'rating' => $booking['rating'],
            'sessions' => $booking['sessions']
        ];
    }, $bookings);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $mentors
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?>