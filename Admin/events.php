<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `user_id`, `title`, `type`, `date`, `time`, `duration`, `mentor`, `attendees`, `priority`, `status`, `created_at`, `updated_at` FROM `events` WHERE 1");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($events);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>