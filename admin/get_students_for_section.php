<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

// Check for required parameters
$section_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$academic_year = isset($_GET['ay']) ? trim($_GET['ay']) : '2024-2025';

if ($section_id <= 0 || empty($academic_year)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid section ID or Academic Year.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$students = [];

try {
    // Query to fetch enrolled students for the given section and year
    $stmt = $conn->prepare("
        SELECT 
            e.student_id,
            sd.school_id,
            u.fullname
        FROM enrollment e
        JOIN student_details sd ON e.student_id = sd.user_id
        JOIN users u ON sd.user_id = u.id
        WHERE e.section_id = ? AND e.academic_year = ?
        ORDER BY u.fullname ASC
    ");
    
    $stmt->bind_param("is", $section_id, $academic_year);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
    
    echo json_encode(['status' => 'success', 'students' => $students]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage(), 'students' => []]);
} finally {
    $conn->close();
}
?>