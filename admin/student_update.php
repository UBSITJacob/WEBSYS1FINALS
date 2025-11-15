<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
$schoolId = trim($_POST['schoolId'] ?? '');
$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$birthdate = trim($_POST['birthdate'] ?? '');
$status = trim($_POST['status'] ?? '');

if (!$id || !$schoolId || !$fullname || !$email || !$gender || !$birthdate) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("UPDATE students SET schoolId=?, fullname=?, email=?, gender=?, birthdate=?, status=? WHERE id=?");
$stmt->bind_param("ssssssi", $schoolId, $fullname, $email, $gender, $birthdate, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Student updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
}
?>
