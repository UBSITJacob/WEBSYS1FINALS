<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$facultyId = trim($_POST['facultyId'] ?? '');
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$status = trim($_POST['status'] ?? '');

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid teacher id.']);
    exit;
}

if ($facultyId === '' || $fullname === '' || $username === '' || $email === '' || $gender === '') {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->begin_transaction();

    // update users table
    $uStmt = $conn->prepare("UPDATE users SET fullname = ?, username = ?, email = ? WHERE id = ?");
    $uStmt->bind_param("sssi", $fullname, $username, $email, $id);
    if (!$uStmt->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user: ' . $uStmt->error]);
        $uStmt->close();
        $conn->close();
        exit;
    }
    $uStmt->close();

    // update teacher_details table
    $tStmt = $conn->prepare("UPDATE teacher_details SET faculty_id = ?, gender = ?, status = ? WHERE user_id = ?");
    $tStmt->bind_param("sssi", $facultyId, $gender, $status, $id);
    if (!$tStmt->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update teacher details: ' . $tStmt->error]);
        $tStmt->close();
        $conn->close();
        exit;
    }
    $tStmt->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Teacher updated successfully!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
