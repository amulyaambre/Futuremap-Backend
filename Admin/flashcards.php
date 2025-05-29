<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `category`, `title`, `duration`, `cost`, `type`, `education_level` FROM `flashcards` WHERE 1");
    $flashcards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($flashcards);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>