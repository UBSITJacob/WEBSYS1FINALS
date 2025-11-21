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
    // check user exists
    $check = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        echo json_encode(['status'=>'error','message'=>'User not found.']);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // reset password to default '1' (plain text) — keep behavior consistent with your app
    $newPassword = '1';
    $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $upd->bind_param("si", $newPassword, $id);

    if ($upd->execute()) {
        echo json_encode(['status'=>'success','message'=>'Password reset to default (1).']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Failed to reset password: '.$upd->error]);
    }
    $upd->close();
} catch (Exception $e) {
    echo json_encode(['status'=>'error','message'=>'Server error: '.$e->getMessage()]);
} finally {
    $conn->close();
}
