<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `user_id`, `mentor_id`, `session_time`, `status`, `created_at`, `updated_at` FROM `session_bookings` WHERE 1");
    $session_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($session_bookings);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>