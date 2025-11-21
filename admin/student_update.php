<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$lrn = trim($_POST['lrn'] ?? '');
$schoolId = trim($_POST['schoolId'] ?? '');
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$contactNo = trim($_POST['contactNo'] ?? '');
$gradeLevel = trim($_POST['gradeLevel'] ?? '');
$status = trim($_POST['status'] ?? '');

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid student id.']);
    exit;
}

// Required fields
if ($lrn === '' || $schoolId === '' || $fullname === '' || $contactNo === '' || $username === '') {
    echo json_encode(['status' => 'error', 'message' => 'LRN, School ID, Full Name, Contact No, and Username are required.']);
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

// Handle grade level (nullable)
$gradeIsNull = ($gradeLevel === '' || strtolower($gradeLevel) === 'null');
if (!$gradeIsNull && !ctype_digit($gradeLevel)) {
    echo json_encode(['status' => 'error', 'message' => 'Grade level must be an integer or empty.']);
    exit;
}
if (!$gradeIsNull) $gradeLevel = intval($gradeLevel);

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->begin_transaction();

    // 1) Update users table including username
    $uStmt = $conn->prepare("
        UPDATE users 
        SET fullname = ?, email = ?, username = ? 
        WHERE id = ?
    ");
    $uStmt->bind_param("sssi", $fullname, $email, $username, $id);

    if (!$uStmt->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user: ' . $uStmt->error]);
        exit;
    }
    $uStmt->close();

    // 2) Ensure LRN and school_id are unique (exclude current user)
    $chk = $conn->prepare("
        SELECT user_id 
        FROM student_details 
        WHERE (lrn = ? OR school_id = ?) AND user_id <> ?
        LIMIT 1
    ");
    $chk->bind_param("ssi", $lrn, $schoolId, $id);
    $chk->execute();
    $chk->store_result();

    if ($chk->num_rows > 0) {
        $chk->close();
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'LRN or School ID already in use by another student.']);
        exit;
    }
    $chk->close();

    // 3) Update student_details
    if ($gradeIsNull) {
        $sStmt = $conn->prepare("
            UPDATE student_details 
            SET lrn = ?, school_id = ?, gender = ?, contact_no = ?, grade_level = NULL, status = ?
            WHERE user_id = ?
        ");
        $sStmt->bind_param("sssssi", $lrn, $schoolId, $gender, $contactNo, $status, $id);
    } else {
        $sStmt = $conn->prepare("
            UPDATE student_details 
            SET lrn = ?, school_id = ?, gender = ?, contact_no = ?, grade_level = ?, status = ?
            WHERE user_id = ?
        ");
        $sStmt->bind_param("ssssisi", $lrn, $schoolId, $gender, $contactNo, $gradeLevel, $status, $id);
    }

    if (!$sStmt->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update student details: ' . $sStmt->error]);
        exit;
    }

    $sStmt->close();
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Student updated successfully!']);

} catch (Exception $e) {
    if ($conn->in_transaction) $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
