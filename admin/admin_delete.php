<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request method']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Invalid ID']);
    exit;
}

$db = new Database();
$conn = $db->conn;

try {
    $conn->begin_transaction();

    // verify existence in admin_details
    $check = $conn->prepare("SELECT user_id FROM admin_details WHERE user_id = ? LIMIT 1");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        $check->close();
        $conn->rollback();
        echo json_encode(['status'=>'error','message'=>'Admin not found in admin_details']);
        $conn->close();
        exit;
    }
    $check->close();

    // delete admin_details
    $d1 = $conn->prepare("DELETE FROM admin_details WHERE user_id = ?");
    $d1->bind_param("i", $id);
    if (!$d1->execute()) {
        $conn->rollback();
        echo json_encode(['status'=>'error','message'=>'Failed to delete admin_details: '.$d1->error]);
        $d1->close();
        $conn->close();
        exit;
    }
    $d1->close();

    // delete user account
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
    echo json_encode(['status'=>'success','message'=>'Admin deleted successfully']);

} catch (Throwable $e) {
    if (isset($conn)) $conn->rollback();
    echo json_encode(['status'=>'error','message'=>'Server error: '.$e->getMessage()]);
} finally {
    if (isset($conn)) $conn->close();
}
?>
