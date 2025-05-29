<?php
// Handle CORS
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection
$host = 'localhost';
$dbname = 'career_platform';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Function to get user_id from token
    function getUserIdFromToken($pdo, $token) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE token = ?');
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $user['id'] : null;
    }

    // Function to get user's education_level
    function getUserEducationLevel($pdo, $user_id) {
        $stmt = $pdo->prepare('SELECT education_level FROM user_profiles WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        return $profile ? $profile['education_level'] : null;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle session booking
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['mentor_id']) || !isset($input['session_time'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: mentor_id or session_time']);
            exit;
        }

        // Get the authorization token
        $headers = apache_request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        $token = str_replace('Bearer ', '', $authHeader);

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'No authentication token provided']);
            exit;
        }

        // Get user_id from token
        $user_id = getUserIdFromToken($pdo, $token);
        if (!$user_id) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }

        // Validate mentor_id
        $stmt = $pdo->prepare('SELECT id FROM mentors WHERE id = ?');
        $stmt->execute([$input['mentor_id']]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            http_response_code(404);
            echo json_encode(['error' => 'Mentor not found']);
            exit;
        }

        // Validate session_time format
        $session_time = DateTime::createFromFormat('Y-m-d H:i:s', $input['session_time']);
        if (!$session_time || $session_time < new DateTime()) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid or past session time']);
            exit;
        }

        // Insert booking into session_bookings
        try {
            $stmt = $pdo->prepare('
                INSERT INTO session_bookings (user_id, mentor_id, session_time, status)
                VALUES (?, ?, ?, ?)
            ');
            $stmt->execute([
                $user_id,
                $input['mentor_id'],
                $input['session_time'],
                'pending'
            ]);

            http_response_code(201);
            echo json_encode(['message' => 'Session booked successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to book session: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get the authorization token
        $headers = apache_request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        $token = str_replace('Bearer ', '', $authHeader);

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'No authentication token provided']);
            exit;
        }

        // Get user_id from token
        $user_id = getUserIdFromToken($pdo, $token);
        if (!$user_id) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }

        // Get user's education_level
        $user_education_level = getUserEducationLevel($pdo, $user_id);
        if (!$user_education_level) {
            http_response_code(404);
            echo json_encode(['error' => 'User profile not found or no education level specified']);
            exit;
        }

        // Handle filter options
        if (isset($_GET['filter'])) {
            $filter = $_GET['filter'];
            try {
                if ($filter === 'specializations') {
                    $stmt = $pdo->prepare('SELECT DISTINCT specialization FROM mentors WHERE education_level = ?');
                    $stmt->execute([$user_education_level]);
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $specializations = [];
                    foreach ($data as $row) {
                        $specs = json_decode($row['specialization'], true);
                        $specializations = array_merge($specializations, is_array($specs) ? $specs : []);
                    }
                    echo json_encode(array_values(array_unique($specializations)));
                    exit;
                } elseif ($filter === 'experience') {
                    $stmt = $pdo->prepare('SELECT DISTINCT experience FROM mentors WHERE education_level = ?');
                    $stmt->execute([$user_education_level]);
                    $data = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    echo json_encode(array_values(array_unique($data)));
                    exit;
                } elseif ($filter === 'languages') {
                    $stmt = $pdo->prepare('SELECT DISTINCT languages FROM mentors WHERE education_level = ?');
                    $stmt->execute([$user_education_level]);
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $languages = [];
                    foreach ($data as $row) {
                        $langs = json_decode($row['languages'], true);
                        $languages = array_merge($languages, is_array($langs) ? $langs : []);
                    }
                    echo json_encode(array_values(array_unique($languages)));
                    exit;
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid filter']);
                    exit;
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to fetch ' . $filter . ': ' . $e->getMessage()]);
                exit;
            }
        }

        // Fetch mentors with filters
        $query = 'SELECT id, name, role, experience, rating, sessions, languages, specialization, avatar, price, education_level, created_at 
                  FROM mentors 
                  WHERE education_level = ?';
        $params = [$user_education_level];

        if (isset($_GET['specialization']) && $_GET['specialization'] !== '') {
            $query .= ' AND JSON_CONTAINS(specialization, ?)';
            $params[] = json_encode($_GET['specialization']);
        }

        if (isset($_GET['experience']) && $_GET['experience'] !== '') {
            $query .= ' AND experience = ?';
            $params[] = $_GET['experience'];
        }

        if (isset($_GET['language']) && $_GET['language'] !== '') {
            $query .= ' AND JSON_CONTAINS(languages, ?)';
            $params[] = json_encode($_GET['language']);
        }

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Decode JSON fields with fallback to empty arrays
            foreach ($mentors as &$mentor) {
                $mentor['languages'] = json_decode($mentor['languages'], true) ?? [];
                $mentor['specialization'] = json_decode($mentor['specialization'], true) ?? [];
                // education_level is a string, no need to decode
            }

            echo json_encode($mentors);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch mentors: ' . $e->getMessage()]);
            exit;
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
}
?>