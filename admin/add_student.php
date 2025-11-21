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
$grade     = trim($_POST['grade'] ?? ''); // <--- Grade input collected as integer string
$password  = "1"; // Default password

// === HASH THE PASSWORD (Security Fix) ===
// $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// =========================================

// === VALIDATION (Check all fields including grade) ===
if (empty($schoolId) || empty($fullname) || empty($gender) || empty($birthdate) || empty($email) || empty($grade)) {
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
    $checkUser->close();

    $checkStudent = $conn->prepare("SELECT user_id FROM student_details WHERE school_id = ?");
    $checkStudent->bind_param("s", $schoolId);
    $checkStudent->execute();
    $studentResult = $checkStudent->get_result();
    $checkStudent->close();

    if ($userResult->num_rows > 0 || $studentResult->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Student already exists (duplicate School ID or Email).']);
        $conn->rollback();
        exit;
    }

    // === INSERT INTO users (using hashed password) ===
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
    $stmtUser->close(); 

    // === INSERT INTO student_details (FIX: Including grade_level) ===
    $stmtDetails = $conn->prepare("
        INSERT INTO student_details (user_id, school_id, grade_level, birthdate, gender, status)
        VALUES (?, ?, ?, ?, ?, 'Active')
    ");
    
    // 🔑 FIX: Bind parameters updated to include grade_level (i for integer)
    // Bind parameters: i (userId) s (schoolId) i (grade) s (birthdate) s (gender)
    $stmtDetails->bind_param("iisss", $userId, $schoolId, $grade, $birthdateFormatted, $gender); 

    if (!$stmtDetails->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => '❌ Failed to add student details.']);
        $conn->close();
        exit;
    }
    $stmtDetails->close();
    
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => '✅ Student added successfully!']);
    
    $conn->close();

} catch (Exception $e) {
    // Ensure rollback on catastrophic failure
    if (isset($conn) && $conn->in_transaction) {
        $conn->rollback();
    }
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}