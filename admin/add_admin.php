<?php
// admin/add_admin.php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$adminId   = trim($_POST['admin_id'] ?? '');
$fullname  = trim($_POST['fullname'] ?? '');
$username  = trim($_POST['username'] ?? '');
$email     = trim($_POST['email'] ?? '');
$gender    = trim($_POST['gender'] ?? '');
$user_type = 'Admin'; // fixed to Admin
$status    = 'Active'; // default

// basic validation
if ($adminId === '' || $fullname === '' || $username === '' || $gender === '') {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields (Admin ID, Full Name, Username, Gender).']);
    exit;
}

if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->conn;
    mysqli_set_charset($conn, "utf8mb4");

    // check username/email duplicates
    $chk = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
    $chk->bind_param("ss", $username, $email);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
        $chk->close();
        echo json_encode(['status' => 'error', 'message' => 'Username or email already exists.']);
        $conn->close();
        exit;
    }
    $chk->close();

    // check admin_id uniqueness
    $chk2 = $conn->prepare("SELECT user_id FROM admin_details WHERE admin_id = ? LIMIT 1");
    $chk2->bind_param("s", $adminId);
    $chk2->execute();
    $chk2->store_result();
    if ($chk2->num_rows > 0) {
        $chk2->close();
        echo json_encode(['status' => 'error', 'message' => 'Admin ID already exists.']);
        $conn->close();
        exit;
    }
    $chk2->close();

    // begin transaction
    $conn->begin_transaction();

    // default password = "1"
    $defaultPass = "1";
    $insUser = $conn->prepare("INSERT INTO users (username, password, email, fullname, user_type) VALUES (?, ?, ?, ?, ?)");
    $insUser->bind_param("sssss", $username, $defaultPass, $email, $fullname, $user_type);
    if (!$insUser->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert user: ' . $insUser->error]);
        $insUser->close();
        $conn->close();
        exit;
    }
    $newUserID = $insUser->insert_id;
    $insUser->close();

    // insert into admin_details
    $insDetails = $conn->prepare("INSERT INTO admin_details (user_id, admin_id, gender, status) VALUES (?, ?, ?, ?)");
    $insDetails->bind_param("isss", $newUserID, $adminId, $gender, $status);
    if (!$insDetails->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert admin details: ' . $insDetails->error]);
        $insDetails->close();
        $conn->close();
        exit;
    }
    $insDetails->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Admin added successfully.']);
    $conn->close();

} catch (Throwable $e) {
    if (isset($conn)) $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
    if (isset($conn)) $conn->close();
}
?>
