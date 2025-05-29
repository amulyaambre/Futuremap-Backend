<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `email`, `phone_number`, `grade`, `stream`, `updated_at`, `address` FROM `user_preferences` WHERE 1");
    $user_preferences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($user_preferences);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>