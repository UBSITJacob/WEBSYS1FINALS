<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
$facultyId = trim($_POST['facultyId'] ?? '');
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$status = trim($_POST['status'] ?? '');

if (!$id || !$facultyId || !$fullname || !$username || !$email || !$gender) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("UPDATE teacher SET facultyId=?, fullname=?, username=?, email=?, gender=?, status=? WHERE id=?");
$stmt->bind_param("ssssssi", $facultyId, $fullname, $username, $email, $gender, $status, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Teacher updated successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
}

$stmt->close();
$conn->close();
?>
