<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$username  = trim($_POST['username'] ?? '');
$lrn       = trim($_POST['lrn'] ?? '');
$schoolId  = trim($_POST['schoolId'] ?? '');
$fullname  = trim($_POST['fullname'] ?? '');
$gender    = trim($_POST['gender'] ?? '');
$contactNo = trim($_POST['contactNo'] ?? '');
$email     = trim($_POST['email'] ?? '');
$grade     = trim($_POST['grade'] ?? '');
$password  = "1";  // Default password

// VALIDATION
if (empty($username) || empty($lrn) || empty($schoolId) || empty($fullname) ||
    empty($gender) || empty($contactNo) || empty($email) || empty($grade)) {

    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    $conn->begin_transaction();

    // --- CHECK DUPLICATE USERNAME ---
    $checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkUsername->bind_param("s", $username);
    $checkUsername->execute();
    $resU = $checkUsername->get_result();
    $checkUsername->close();

    if ($resU->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists.']);
        $conn->rollback();
        exit;
    }

    // --- CHECK DUPLICATE EMAIL ---
    $checkUser = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $resEmail = $checkUser->get_result();
    $checkUser->close();

    if ($resEmail->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
        $conn->rollback();
        exit;
    }

    // --- CHECK DUPLICATE LRN / SCHOOL ID ---
    $checkStudent = $conn->prepare("SELECT user_id FROM student_details WHERE lrn = ? OR school_id = ?");
    $checkStudent->bind_param("ss", $lrn, $schoolId);
    $checkStudent->execute();
    $resStudent = $checkStudent->get_result();
    $checkStudent->close();

    if ($resStudent->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Duplicate LRN or School ID.']);
        $conn->rollback();
        exit;
    }

    // INSERT USER WITH MANUAL USERNAME
    $stmtUser = $conn->prepare("
        INSERT INTO users (username, password, email, fullname, user_type)
        VALUES (?, ?, ?, ?, 'Student')
    ");
    $stmtUser->bind_param("ssss", $username, $password, $email, $fullname);
    $stmtUser->execute();
    $userId = $conn->insert_id;
    $stmtUser->close();

    // INSERT STUDENT DETAILS
    $stmtDetails = $conn->prepare("
        INSERT INTO student_details (user_id, school_id, lrn, grade_level, contact_no, gender, status)
        VALUES (?, ?, ?, ?, ?, ?, 'Active')
    ");
    $stmtDetails->bind_param("ississ", $userId, $schoolId, $lrn, $grade, $contactNo, $gender);
    $stmtDetails->execute();
    $stmtDetails->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Student added successfully!']);

} catch (Exception $e) {
    if (isset($conn) && $conn->in_transaction) {
        $conn->rollback();
    }
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
