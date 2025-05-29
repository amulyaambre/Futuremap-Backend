<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `first_name`, `last_name`, `email`, `role`, `created_at`, `phone_number`, `address` FROM `users` WHERE 1");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>
