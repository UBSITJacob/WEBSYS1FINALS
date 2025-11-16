<?php
// admin/update_account.php
session_start();
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

// Check login
if (!isset($_SESSION['admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

// session admin expected to be an array with 'id' etc.
$adminSession = $_SESSION['admin'];
$adminId = is_array($adminSession) ? intval($adminSession['id']) : intval($adminSession);
if ($adminId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session.']);
    exit;
}

// Get posted fields
$newFullname = trim($_POST['new_fullname'] ?? '');
$newUsername = trim($_POST['new_username'] ?? '');
$newEmail    = trim($_POST['new_email'] ?? '');
$oldPassword = trim($_POST['old_password'] ?? '');
$newPassword = trim($_POST['new_password'] ?? '');
$confirmPass = trim($_POST['confirm_password'] ?? '');

// Basic validation
if ($oldPassword === '') {
    echo json_encode(['status' => 'error', 'message' => 'Current password is required.']);
    exit;
}

// Connect DB
$db = new Database();
$conn = $db->getConnection();

// Fetch current user record from users table
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$res = $stmt->get_result();
$current = $res->fetch_assoc();
$stmt->close();

if (!$current) {
    echo json_encode(['status' => 'error', 'message' => 'User record not found.']);
    exit;
}

// Ensure this user is an Admin
if (!isset($current['user_type']) || strtolower($current['user_type']) !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized: not an admin account.']);
    exit;
}

// Verify current password (plain text comparison as requested)
if ($current['password'] !== $oldPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect current password.']);
    exit;
}

// Validate new email if provided
if ($newEmail !== '' && !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

// Validate new password if provided
if ($newPassword !== '') {
    if (strlen($newPassword) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long.']);
        exit;
    }
    if ($newPassword !== $confirmPass) {
        echo json_encode(['status' => 'error', 'message' => 'New passwords do not match.']);
        exit;
    }
}

// Check username uniqueness (if changed)
if ($newUsername !== '' && $newUsername !== $current['username']) {
    $chk = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
    $chk->bind_param("si", $newUsername, $adminId);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already taken.']);
        $chk->close();
        exit;
    }
    $chk->close();
}

// Check email uniqueness if needed (optional) - only if email changed and not empty
if ($newEmail !== '' && $newEmail !== $current['email']) {
    $chk = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
    $chk->bind_param("si", $newEmail, $adminId);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already in use.']);
        $chk->close();
        exit;
    }
    $chk->close();
}

// Build update dynamically
$updates = [];
$params = [];
$types = '';

if ($newUsername !== '' && $newUsername !== $current['username']) {
    $updates[] = "username = ?";
    $params[] = $newUsername;
    $types .= 's';
}

if ($newFullname !== '' && $newFullname !== $current['fullname']) {
    $updates[] = "fullname = ?";
    $params[] = $newFullname;
    $types .= 's';
}

if ($newEmail !== '' && $newEmail !== $current['email']) {
    $updates[] = "email = ?";
    $params[] = $newEmail;
    $types .= 's';
}

if ($newPassword !== '') {
    $updates[] = "password = ?";
    $params[] = $newPassword; // plain text - per instruction
    $types .= 's';
}

if (empty($updates)) {
    echo json_encode(['status' => 'error', 'message' => 'No changes detected.']);
    exit;
}

// append id
$params[] = $adminId;
$types .= 'i';

$sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
$updateStmt = $conn->prepare($sql);
if ($updateStmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

// bind params dynamically
$bind_names[] = $types;
for ($i = 0; $i < count($params); $i++) {
    $bind_names[] = &$params[$i];
}
call_user_func_array([$updateStmt, 'bind_param'], $bind_names);

$exec = $updateStmt->execute();
if ($exec) {
    // Refresh session values
    if ($newUsername !== '') $_SESSION['admin']['username'] = $newUsername;
    if ($newFullname !== '') $_SESSION['admin']['fullname'] = $newFullname;
    if ($newEmail    !== '') $_SESSION['admin']['email']    = $newEmail;
    if ($newPassword !== '') $_SESSION['admin']['password'] = $newPassword;

    echo json_encode(['status' => 'success', 'message' => 'Account updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $updateStmt->error]);
}

$updateStmt->close();
$conn->close();
