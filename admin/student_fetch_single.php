<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
$schoolId = isset($_REQUEST['schoolId']) ? trim($_REQUEST['schoolId']) : '';

if ($id === '' && $schoolId === '') {
    echo json_encode(['status' => 'error', 'message' => 'Provide id or schoolId.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    if ($id !== '') {
        $userId = intval($id);
        if ($userId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid id.']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT sd.user_id AS id, sd.lrn AS lrn, sd.school_id AS schoolId,
                   sd.grade_level AS gradeLevel, sd.gender, sd.contact_no AS contactNo,
                   sd.status, u.fullname, u.email, u.username /* <--- USERNAME ADDED/CONFIRMED */
            FROM student_details sd
            LEFT JOIN users u ON u.id = sd.user_id
            WHERE sd.user_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $userId);
    } else {
        $stmt = $conn->prepare("
            SELECT sd.user_id AS id, sd.lrn AS lrn, sd.school_id AS schoolId,
                   sd.grade_level AS gradeLevel, sd.gender, sd.contact_no AS contactNo,
                   sd.status, u.fullname, u.email, u.username /* <<< USERNAME ADDED/CONFIRMED */
            FROM student_details sd
            LEFT JOIN users u ON u.id = sd.user_id
            WHERE sd.school_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $schoolId);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $student = $res->fetch_assoc();

        // Fix null fields -> empty string
        foreach (['schoolId','fullname','email','gender','contactNo','lrn','status','username'] as $k) {
            $student[$k] = $student[$k] ?? '';
        }

        // gradeLevel conversion
        $student['gradeLevel'] = $student['gradeLevel'] !== null ? (int)$student['gradeLevel'] : null;

        echo json_encode([
            'status' => 'success',
            'message' => 'Student fetched',
            'student' => $student
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Student not found.']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
} finally {
    $conn->close();
}