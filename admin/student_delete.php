<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid student ID']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// verify record exists
$check = $conn->prepare("SELECT id FROM students WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Student not found']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// delete record
$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Delete failed: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
?>
