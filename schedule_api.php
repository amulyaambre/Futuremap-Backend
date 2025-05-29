<?php
// Handle CORS
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
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

    // Extract token from Authorization header
    $headers = apache_request_headers();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    $token = str_replace('Bearer ', '', $authHeader);

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'No authentication token provided']);
        exit;
    }

    // Get user_id
    $user_id = getUserIdFromToken($pdo, $token);
    if (!$user_id) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid or expired token']);
        exit;
    }

    // Handle requests
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Fetch events for the authenticated user
            try {
                $stmt = $pdo->prepare('
                    SELECT id, title, type, date, time, duration, mentor, attendees, priority, status
                    FROM events
                    WHERE user_id = ?
                    ORDER BY date ASC, time ASC
                ');
                $stmt->execute([$user_id]);
                $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Format date and time for frontend
                foreach ($events as &$event) {
                    $event['date'] = (new DateTime($event['date']))->format('Y-m-d');
                    $event['time'] = (new DateTime($event['time']))->format('H:i');
                }

                http_response_code(200);
                echo json_encode($events);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to fetch events: ' . $e->getMessage()]);
            }
            break;

        case 'POST':
            // Create a new event
            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['title'], $input['type'], $input['date'], $input['time'], $input['status'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields: title, type, date, time, status']);
                exit;
            }

            // Validate inputs
            $valid_types = ['mentor', 'deadline', 'event'];
            $valid_statuses = ['confirmed', 'pending', 'registered', 'cancelled'];
            $valid_priorities = ['low', 'medium', 'high', null];

            if (!in_array($input['type'], $valid_types)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid event type']);
                exit;
            }
            if (!in_array($input['status'], $valid_statuses)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status']);
                exit;
            }
            if (isset($input['priority']) && !in_array($input['priority'], $valid_priorities)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid priority']);
                exit;
            }

            // Validate date and time formats
            $date = DateTime::createFromFormat('Y-m-d', $input['date']);
            $time = DateTime::createFromFormat('H:i', $input['time']);
            if (!$date || !$time) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid date or time format']);
                exit;
            }

            try {
                $stmt = $pdo->prepare('
                    INSERT INTO events (user_id, title, type, date, time, duration, mentor, attendees, priority, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ');
                $stmt->execute([
                    $user_id,
                    $input['title'],
                    $input['type'],
                    $input['date'],
                    $input['time'],
                    $input['duration'] ?? null,
                    $input['mentor'] ?? null,
                    isset($input['attendees']) ? (int)$input['attendees'] : null,
                    $input['priority'] ?? null,
                    $input['status']
                ]);

                $event_id = $pdo->lastInsertId();
                http_response_code(201);
                echo json_encode(['message' => 'Event created successfully', 'id' => $event_id]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create event: ' . $e->getMessage()]);
            }
            break;

        case 'PUT':
            // Update an existing event
            $input = json_decode(file_get_contents('php://input'), true);
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $segments = explode('/', trim($path, '/'));
            $event_id = end($segments);

            if (!is_numeric($event_id)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid event ID']);
                exit;
            }

            // Verify event exists and belongs to user
            $stmt = $pdo->prepare('SELECT id FROM events WHERE id = ? AND user_id = ?');
            $stmt->execute([$event_id, $user_id]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                http_response_code(404);
                echo json_encode(['error' => 'Event not found or unauthorized']);
                exit;
            }

            // Validate inputs (at least one field required)
            if (empty($input)) {
                http_response_code(400);
                echo json_encode(['error' => 'No fields provided for update']);
                exit;
            }

            $allowed_fields = ['title', 'type', 'date', 'time', 'duration', 'mentor', 'attendees', 'priority', 'status'];
            $update_fields = [];
            $params = [];

            foreach ($input as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    if ($key === 'type' && !in_array($value, $valid_types)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid event type']);
                        exit;
                    }
                    if ($key === 'status' && !in_array($value, $valid_statuses)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid status']);
                        exit;
                    }
                    if ($key === 'priority' && !in_array($value, $valid_priorities)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid priority']);
                        exit;
                    }
                    if ($key === 'date' && !DateTime::createFromFormat('Y-m-d', $value)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid date format']);
                        exit;
                    }
                    if ($key === 'time' && !DateTime::createFromFormat('H:i', $value)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid time format']);
                        exit;
                    }
                    if ($key === 'attendees' && !is_numeric($value)) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Attendees must be numeric']);
                        exit;
                    }

                    $update_fields[] = "$key = ?";
                    $params[] = $value;
                }
            }

            if (empty($update_fields)) {
                http_response_code(400);
                echo json_encode(['error' => 'No valid fields provided for update']);
                exit;
            }

            $params[] = $event_id;
            $params[] = $user_id;

            try {
                $stmt = $pdo->prepare('
                    UPDATE events
                    SET ' . implode(', ', $update_fields) . '
                    WHERE id = ? AND user_id = ?
                ');
                $stmt->execute($params);

                http_response_code(200);
                echo json_encode(['message' => 'Event updated successfully']);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update event: ' . $e->getMessage()]);
            }
            break;

        case 'DELETE':
            // Delete an event
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $segments = explode('/', trim($path, '/'));
            $event_id = end($segments);

            if (!is_numeric($event_id)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid event ID']);
                exit;
            }

            // Verify event exists and belongs to user
            $stmt = $pdo->prepare('SELECT id FROM events WHERE id = ? AND user_id = ?');
            $stmt->execute([$event_id, $user_id]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                http_response_code(404);
                echo json_encode(['error' => 'Event not found or unauthorized']);
                exit;
            }

            try {
                $stmt = $pdo->prepare('DELETE FROM events WHERE id = ? AND user_id = ?');
                $stmt->execute([$event_id, $user_id]);

                http_response_code(200);
                echo json_encode(['message' => 'Event deleted successfully']);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete event: ' . $e->getMessage()]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
}
    ?>