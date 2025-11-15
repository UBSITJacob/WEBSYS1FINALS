<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid student ID.']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Verify record exists
    $check = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Student not found.']);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // Reset password to default '1'
    $newPassword = '1';
    $update = $conn->prepare("UPDATE students SET password = ? WHERE id = ?");
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
