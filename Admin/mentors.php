<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `name`, `role`, `experience`, `rating`, `sessions`, `languages`, `specialization`, `avatar`, `price`, `created_at`, `education_level` FROM `mentors` WHERE 1");
    $mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($mentors);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>