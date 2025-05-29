<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `type`, `value` FROM `grade_stream_options` WHERE 1");
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($options);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>