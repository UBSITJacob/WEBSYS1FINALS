<?php
session_start();
require_once "oop_functions.php";

// Check if student is logged in
if (!isset($_SESSION['student'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$student_id = $_SESSION['student']['id'];
$student = new Student($student_id);

// Get form data
$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

// Update password and return result as JSON
$result = $student->updatePassword($old_password, $new_password);

echo json_encode($result);
exit;
