<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

$student_ids = $_POST['student_ids'] ?? [];
$section_id = isset($_POST['section_id']) ? intval($_POST['section_id']) : 0;
$academic_year = isset($_POST['academic_year']) ? trim($_POST['academic_year']) : '2024-2025';

if ($section_id <= 0 || empty($student_ids)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid section ID or no students selected.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$success_count = 0;
$error_count = 0;

try {
    $conn->begin_transaction();

    // Prepare the enrollment statement
    $stmt = $conn->prepare("
        INSERT INTO enrollment (student_id, section_id, academic_year)
        VALUES (?, ?, ?)
    ");

    foreach ($student_ids as $student_id) {
        $student_id_int = intval($student_id);

        if ($student_id_int > 0) {
            // Check for existing enrollment (optional, but good for safety)
            // If the student is already enrolled, we skip/ignore them. 
            // The unassigned student query should have filtered them out, but this is a double check.
            
            if ($stmt->bind_param("iis", $student_id_int, $section_id, $academic_year) && $stmt->execute()) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
    }
    $stmt->close();

    if ($error_count == 0) {
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => "Successfully enrolled $success_count student(s) into Section ID $section_id."]);
    } else {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => "Failed to enroll all students. Total errors: $error_count."]);
    }

} catch (Exception $e) {
    if (isset($conn) && $conn->in_transaction) {
        $conn->rollback();
    }
    echo json_encode(['status' => 'error', 'message' => 'Transaction error: ' . $e->getMessage()]);
} finally {
    $conn->close();
}