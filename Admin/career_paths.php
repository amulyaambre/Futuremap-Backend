<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `user_id`, `path_id`, `cards`, `connections`, `created_at`, `updated_at` FROM `career_paths` WHERE 1");
    $career_paths = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($career_paths);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>