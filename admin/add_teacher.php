<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

// ✅ Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// ✅ Collect and sanitize input
$facultyId = trim($_POST['facultyId'] ?? '');
$fullname  = trim($_POST['fullname'] ?? '');
$username  = trim($_POST['username'] ?? '');
$gender    = trim($_POST['gender'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = '1'; // default password

// ✅ Validate required fields
if (!$facultyId || !$fullname || !$username || !$gender || !$email) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

// ✅ Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    mysqli_set_charset($conn, "utf8mb4");

    // === Check duplicates (username/email in users, facultyId in teacher_details) ===
    $checkUser = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $checkUser->bind_param("ss", $username, $email);
    $checkUser->execute();
    $userResult = $checkUser->get_result();

    $checkTeacher = $conn->prepare("SELECT user_id FROM teacher_details WHERE faculty_id = ?");
    $checkTeacher->bind_param("s", $facultyId);
    $checkTeacher->execute();
    $teacherResult = $checkTeacher->get_result();

    if (($userResult && $userResult->num_rows > 0) || ($teacherResult && $teacherResult->num_rows > 0)) {
        echo json_encode(['status' => 'error', 'message' => 'Teacher already exists (duplicate Faculty ID, Username, or Email).']);
        $checkUser->close();
        $checkTeacher->close();
        $conn->close();
        exit;
    }
    $checkUser->close();
    $checkTeacher->close();

    // === Insert into users ===
    $stmtUser = $conn->prepare("
        INSERT INTO users (username, password, email, fullname, user_type)
        VALUES (?, ?, ?, ?, 'Teacher')
    ");
    $stmtUser->bind_param("ssss", $username, $password, $email, $fullname);

    if (!$stmtUser->execute()) {
        echo json_encode(['status' => 'error', 'message' => '❌ Failed to add teacher. Database error.']);
        $stmtUser->close();
        $conn->close();
        exit;
    }
    $userId = $conn->insert_id;

    // === Insert into teacher_details ===
    $stmtDetails = $conn->prepare("
        INSERT INTO teacher_details (user_id, faculty_id, gender, status)
        VALUES (?, ?, ?, 'Active')
    ");
    $stmtDetails->bind_param("iss", $userId, $facultyId, $gender);

    if ($stmtDetails->execute()) {
        echo json_encode(['status' => 'success', 'message' => '✅ Teacher added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '❌ Failed to add teacher details.']);
    }

    $stmtUser->close();
    $stmtDetails->close();
    $conn->close();

} catch (Throwable $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
