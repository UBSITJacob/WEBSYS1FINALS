<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$schoolId = isset($_POST['schoolId']) ? trim($_POST['schoolId']) : '';
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Invalid student id.']);
    exit;
}

// basic validation
if ($schoolId === '' || $fullname === '' || $email === '' || $gender === '' || $birthdate === '') {
    echo json_encode(['status'=>'error','message'=>'All fields are required.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    // Begin transaction
    $conn->begin_transaction();

    // 1) Update users table (fullname, email)
    $uStmt = $conn->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?");
    $uStmt->bind_param("ssi", $fullname, $email, $id);
    if (!$uStmt->execute()) {
        $conn->rollback();
        echo json_encode(['status'=>'error','message'=>'Failed to update user: '.$uStmt->error]);
        $uStmt->close();
        $conn->close();
        exit;
    }
    $uStmt->close();

    // 2) Update student_details table
    $sStmt = $conn->prepare("UPDATE student_details SET school_id = ?, gender = ?, birthdate = ?, status = ? WHERE user_id = ?");
    $sStmt->bind_param("ssssi", $schoolId, $gender, $birthdate, $status, $id);
    if (!$sStmt->execute()) {
        $conn->rollback();
        echo json_encode(['status'=>'error','message'=>'Failed to update student details: '.$sStmt->error]);
        $sStmt->close();
        $conn->close();
        exit;
    }
    $sStmt->close();

    $conn->commit();

    echo json_encode(['status'=>'success','message'=>'Student updated successfully!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status'=>'error','message'=>'Server error: '.$e->getMessage()]);
} finally {
    $conn->close();
}
