<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

// Accept both GET and POST for flexibility
$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
$schoolId = isset($_REQUEST['schoolId']) ? trim($_REQUEST['schoolId']) : '';

if ($id === '' && $schoolId === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing parameter: provide either id or schoolId.'
    ]);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    // Build query dynamically
    if ($id !== '') {
        $numericId = intval($id);
        if ($numericId <= 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid ID parameter.'
            ]);
            exit;
        }
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $numericId);
    } else {
        $stmt = $conn->prepare("SELECT * FROM students WHERE schoolId = ? LIMIT 1");
        $stmt->bind_param("s", $schoolId);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $student = $res->fetch_assoc();

        // Add fallbacks to prevent UI issues
        if (empty($student['status'])) $student['status'] = 'Active';
        if (empty($student['profile_pic'])) $student['profile_pic'] = 'img/default.jpg';

        echo json_encode([
            'status' => 'success',
            'message' => 'Student record fetched successfully.',
            'student' => $student
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Student not found.'
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
} finally {
    $conn->close();
}
?>
