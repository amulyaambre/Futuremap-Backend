<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `user_id`, `education_level`, `score_range`, `interests`, `budget`, `timeline`, `created_at`, `updated_at` FROM `user_profiles` WHERE 1");
    $user_profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($user_profiles);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>