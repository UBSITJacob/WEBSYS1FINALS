<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

// Check for required parameters
$grade_level = isset($_GET['grade']) ? intval($_GET['grade']) : 0;
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$academic_year = '2024-2025'; // Consistent Academic Year

if ($grade_level <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid Grade Level.', 'students' => []]);
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$students = [];

try {
    $search_param = '%' . $query . '%';

    // Query students who:
    // 1. Match the required grade_level in student_details.
    // 2. Are NOT currently enrolled in ANY section for the current academic year (using LEFT JOIN / WHERE NULL check).
    $sql = "
        SELECT 
            u.id, 
            u.fullname,
            sd.school_id
        FROM users u
        JOIN student_details sd ON u.id = sd.user_id
        LEFT JOIN enrollment e ON u.id = e.student_id AND e.academic_year = ?
        WHERE sd.grade_level = ?
        AND e.student_id IS NULL
    ";
    
    // Add search filter if query is provided
    if (!empty($query)) {
        $sql .= " AND (u.fullname LIKE ? OR sd.school_id LIKE ?)";
    }
    
    $sql .= " ORDER BY u.fullname ASC";

    $stmt = $conn->prepare($sql);
    
    // Bind parameters dynamically based on search query presence
    if (!empty($query)) {
        $stmt->bind_param("iisss", $academic_year, $grade_level, $search_param, $search_param);
    } else {
        $stmt->bind_param("is", $academic_year, $grade_level);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Map ID to 'id' for use in checkboxes
        $students[] = ['id' => $row['id'], 'fullname' => $row['fullname'], 'school_id' => $row['school_id']];
    }
    $stmt->close();
    
    echo json_encode(['status' => 'success', 'students' => $students]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage(), 'students' => []]);
} finally {
    $conn->close();
}