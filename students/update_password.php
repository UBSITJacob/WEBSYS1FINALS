<?php
session_start();
require_once "oop_functions.php"; // Ensure your database connection and functions are included

// Check if student is logged in
if (!isset($_SESSION['student'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit;
}

$student_id = $_SESSION['student']['id'];
$student = new Student($student_id);

// Get form data
$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];

// Fetch the stored password hash for the current user
$storedHash = $student->getPasswordHash(); // Make sure this function returns the correct hashed password

// Validate old password
if (password_verify($old_password, $storedHash)) {
    // Old password is correct, proceed with password update
    if ($new_password === $_POST['confirm_password']) {
        // Hash new password
        $newPasswordHash = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in database
        if ($student->updatePassword($newPasswordHash)) {
            echo json_encode(['status' => 'success', 'message' => 'Password updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'New passwords do not match.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect.']);
}
?>
