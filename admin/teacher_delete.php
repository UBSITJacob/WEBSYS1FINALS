<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid teacher ID']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->begin_transaction();

    // verify existence
    $check = $conn->prepare("SELECT user_id FROM teacher_details WHERE user_id = ? LIMIT 1");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        $check->close();
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Teacher not found in teacher_details']);
        $conn->close();
        exit;
    }
    $check->close();

    // delete teacher_details
    $d1 = $conn->prepare("DELETE FROM teacher_details WHERE user_id = ?");
    $d1->bind_param("i", $id);
    if (!$d1->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete teacher_details: ' . $d1->error]);
        $d1->close();
        $conn->close();
        exit;
    }
    $d1->close();

    // delete user account (optional; we keep same behavior as students)
    $d2 = $conn->prepare("DELETE FROM users WHERE id = ?");
    $d2->bind_param("i", $id);
    if (!$d2->execute()) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete user: ' . $d2->error]);
        $d2->close();
        $conn->close();
        exit;
    }
    $d2->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Teacher deleted successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
