<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "Evelio_AMS_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

$admin = $_SESSION['admin'];
$adminId = is_array($admin) ? $admin['id'] : $admin;

$currentPassword = $_POST['currentPassword'] ?? '';
$newUsername     = trim($_POST['newUsername'] ?? '');
$newPassword     = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// Fetch current admin info
$stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Admin not found."]);
    exit;
}

// Simple password verification (no hashing)
if ($currentPassword !== $user['password']) {
    echo json_encode(["status" => "error", "message" => "Incorrect current password."]);
    exit;
}

// Validate new password
if (!empty($newPassword) || !empty($confirmPassword)) {
    if ($newPassword !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "New passwords do not match."]);
        exit;
    }
} else {
    $newPassword = $user['password']; // keep old
}

if (empty($newUsername)) {
    $newUsername = $user['username'];
}

// Update account
$updateStmt = $conn->prepare("UPDATE admin SET username = ?, password = ? WHERE id = ?");
$updateStmt->bind_param("ssi", $newUsername, $newPassword, $adminId);

if ($updateStmt->execute()) {
    $_SESSION['admin']['username'] = $newUsername;
    echo json_encode(["status" => "success", "message" => "Account updated successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update account."]);
}

$updateStmt->close();
$conn->close();
?>
