<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['name'] ?? '';
    $role = $data['role'] ?? '';
    $experience = $data['experience'] ?? '';
    $rating = $data['rating'] ?? 0;
    $sessions = $data['sessions'] ?? 0;
    $languages = $data['languages'] ?? '';
    $specialization = $data['specialization'] ?? '';
    $avatar = $data['avatar'] ?? '';
    $price = $data['price'] ?? 0;
    $education_level = $data['education_level'] ?? '';

    if (empty($name) || empty($role) || empty($specialization)) {
        echo json_encode(['error' => 'Required fields are missing']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO `mentors` (
                `name`, `role`, `experience`, `rating`, `sessions`, 
                `languages`, `specialization`, `avatar`, `price`, 
                `education_level`, `created_at`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $name, $role, $experience, $rating, $sessions,
            $languages, $specialization, $avatar, $price,
            $education_level
        ]);
        echo json_encode(['success' => 'Mentor added successfully']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to add mentor: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>