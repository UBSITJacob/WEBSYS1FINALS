<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$schoolId  = trim($_POST['schoolId'] ?? '');
$fullname  = trim($_POST['fullname'] ?? '');
$gender    = trim($_POST['gender'] ?? '');
$birthdate = trim($_POST['birthdate'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = "1"; // Default password (no hashing)

// === VALIDATION ===
if (empty($schoolId) || empty($fullname) || empty($gender) || empty($birthdate) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

// === FORMAT BIRTHDATE ===
$birthdateFormatted = date('Y-m-d', strtotime($birthdate));
if ($birthdateFormatted === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid birthdate format.']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $conn->begin_transaction();

    // === CHECK DUPLICATE SCHOOL ID OR EMAIL ===
    $checkUser = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $userResult = $checkUser->get_result();

    $checkStudent = $conn->prepare("SELECT user_id FROM student_details WHERE school_id = ?");
    $checkStudent->bind_param("s", $schoolId);
    $checkStudent->execute();
    $studentResult = $checkStudent->get_result();

    if ($userResult->num_rows > 0 || $studentResult->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Student already exists (duplicate School ID or Email).']);
        $conn->rollback();
        exit;
    }

    // === INSERT INTO users ===
    $stmtUser = $conn->prepare("
        INSERT INTO users (username, password, email, fullname, user_type)
        VALUES (?, ?, ?, ?, 'Student')
    ");
    $stmtUser->bind_param("ssss", $email, $password, $email, $fullname);

    if (!$stmtUser->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create user account.']);
        $conn->rollback();
        exit;
    }

    $userId = $conn->insert_id;

    // === INSERT INTO student_details ===
    $stmtDetails = $conn->prepare("
        INSERT INTO student_details (user_id, school_id, birthdate, gender, status)
        VALUES (?, ?, ?, ?, 'Active')
    ");
    $stmtDetails->bind_param("isss", $userId, $schoolId, $birthdateFormatted, $gender);

    if ($stmtDetails->execute()) {
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => '✅ Student added successfully!']);
    } else {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => '❌ Failed to add student details.']);
    }

    $stmtUser->close();
    $stmtDetails->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
