<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request method.']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Invalid student id.']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->begin_transaction();

    // delete student_details row
    $d1 = $conn->prepare("DELETE FROM student_details WHERE user_id = ?");
    $d1->bind_param("i", $id);
    if (!$d1->execute()) {
        $conn->rollback();
        echo json_encode(['status'=>'error','message'=>'Failed to delete student_details: '.$d1->error]);
        $d1->close();
        $conn->close();
        exit;
    }
    $d1->close();

    // delete users row (if you want to keep the user row, remove this block)
    $d2 = $conn->prepare("DELETE FROM users WHERE id = ?");
    $d2->bind_param("i", $id);
    if (!$d2->execute()) {
        $conn->rollback();
        echo json_encode(['status'=>'error','message'=>'Failed to delete user: '.$d2->error]);
        $d2->close();
        $conn->close();
        exit;
    }
    $d2->close();

    $conn->commit();
    echo json_encode(['status'=>'success','message'=>'Student deleted successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status'=>'error','message'=>'Server error: '.$e->getMessage()]);
} finally {
    $conn->close();
}
