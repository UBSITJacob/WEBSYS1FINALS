<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid teacher ID.']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $check = $conn->prepare("SELECT id FROM teacher WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Teacher not found.']);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // reset password to default '1'
    $newPassword = '1';
    $update = $conn->prepare("UPDATE teacher SET password = ? WHERE id = ?");
    $update->bind_param("si", $newPassword, $id);

    if ($update->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Password reset to default (1).']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
    }

    $update->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
