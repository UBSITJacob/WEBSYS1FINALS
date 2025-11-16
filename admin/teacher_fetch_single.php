<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : '';
$facultyId = isset($_REQUEST['facultyId']) ? trim($_REQUEST['facultyId']) : '';

if ($id === '' && $facultyId === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameter: provide either id or facultyId.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    if ($id !== '') {
        $numericId = intval($id);
        if ($numericId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID parameter.']);
            exit;
        }
        $stmt = $conn->prepare("
            SELECT td.user_id AS id, td.faculty_id AS facultyId, td.gender, td.status,
                   u.fullname, u.email, u.username
            FROM teacher_details td
            LEFT JOIN users u ON u.id = td.user_id
            WHERE td.user_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $numericId);
    } else {
        $stmt = $conn->prepare("
            SELECT td.user_id AS id, td.faculty_id AS facultyId, td.gender, td.status,
                   u.fullname, u.email, u.username
            FROM teacher_details td
            LEFT JOIN users u ON u.id = td.user_id
            WHERE td.faculty_id = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $facultyId);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $teacher = $res->fetch_assoc();
        // normalize
        $teacher['facultyId'] = $teacher['facultyId'] ?? '';
        $teacher['fullname'] = $teacher['fullname'] ?? '';
        $teacher['email'] = $teacher['email'] ?? '';
        $teacher['username'] = $teacher['username'] ?? '';
        $teacher['gender'] = $teacher['gender'] ?? '';
        $teacher['status'] = $teacher['status'] ?? 'Active';

        echo json_encode(['status' => 'success', 'message' => 'Teacher record fetched successfully.', 'teacher' => $teacher]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Teacher not found.']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
