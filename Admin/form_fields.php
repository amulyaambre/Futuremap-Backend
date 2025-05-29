<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT `id`, `field_name`, `field_label`, `field_type`, `options`, `icon`, `is_required`, `display_order`, `created_at` FROM `form_fields` WHERE 1");
    $form_fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($form_fields);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>