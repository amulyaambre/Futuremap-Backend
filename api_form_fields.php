<?php
// Set headers for JSON response and CORS
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *'); // Allow all origins (adjust for production)
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
 $host = 'localhost';
$dbname = 'career_platform';
$username = 'root'; // Replace with your MySQL username
$password = ''; // Replace with your MySQL password

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Query to fetch all form fields
    $query = 'SELECT id, field_name, field_label, field_type, options, icon, is_required, display_order 
              FROM form_fields 
              ORDER BY display_order ASC';
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Fetch all rows
    $formFields = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decode JSON options field for each row
    foreach ($formFields as &$field) {
        $field['options'] = json_decode($field['options'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON in options field for field_id: ' . $field['id']);
        }
    }

    // Return JSON response
    echo json_encode($formFields);

} catch (PDOException $e) {
    // Handle database errors
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Handle other errors (e.g., JSON parsing)
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>