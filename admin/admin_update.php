<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request method']);
    exit;
}

$id        = intval($_POST['id'] ?? 0);
$adminId   = trim($_POST['admin_id'] ?? '');
$fullname  = trim($_POST['fullname'] ?? '');
$username  = trim($_POST['username'] ?? '');
$email     = trim($_POST['email'] ?? '');
$gender    = trim($_POST['gender'] ?? '');
$status    = trim($_POST['status'] ?? 'Active');
$user_type = 'Admin'; // fixed

if ($id <= 0 || !$adminId || !$fullname || !$username) {
    echo json_encode(['status'=>'error','message'=>'Missing required fields']);
    exit;
}

if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status'=>'error','message'=>'Invalid email']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->conn;
    mysqli_set_charset($conn, 'utf8mb4');

    // check username/email uniqueness excluding current user
    $chk = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ? LIMIT 1");
    $chk->bind_param("ssi", $username, $email, $id);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
        $chk->close();
        echo json_encode(['status'=>'error','message'=>'Username or email already in use by another account']);
        $conn->close();
        exit;
    }
    $chk->close();

    // check admin_id uniqueness (exclude current user_id)
    $chk2 = $conn->prepare("SELECT user_id FROM admin_details WHERE admin_id = ? AND user_id != ? LIMIT 1");
    $chk2->bind_param("si", $adminId, $id);
    $chk2->execute();
    $chk2->store_result();
    if ($chk2->num_rows > 0) {
        $chk2->close();
        echo json_encode(['status'=>'error','message'=>'Admin ID already in use by another account']);
        $conn->close();
        exit;
    }
    $chk2->close();

    $conn->begin_transaction();

    // update users
    $updUser = $conn->prepare("UPDATE users SET fullname = ?, username = ?, email = ?, user_type = ? WHERE id = ?");
    $updUser->bind_param("ssssi", $fullname, $username, $email, $user_type, $id);
    if (!$updUser->execute()) {
        $conn->rollback();
        echo json_encode(['status'=>'error','message'=>'Failed to update user: '.$updUser->error]);
        $updUser->close();
        $conn->close();
        exit;
    }
    $updUser->close();

    // update admin_details
    $updAd = $conn->prepare("UPDATE admin_details SET admin_id = ?, gender = ?, status = ? WHERE user_id = ?");
    $updAd->bind_param("sssi", $adminId, $gender, $status, $id);
    if (!$updAd->execute()) {
        $conn->rollback();
        echo json_encode(['status'=>'error','message'=>'Failed to update admin details: '.$updAd->error]);
        $updAd->close();
        $conn->close();
        exit;
    }
    $updAd->close();

    $conn->commit();
    echo json_encode(['status'=>'success','message'=>'Admin updated successfully']);
    $conn->close();

} catch (Throwable $e) {
    if (isset($conn) && $conn->errno) $conn->rollback();
    echo json_encode(['status'=>'error','message'=>'Server error: '.$e->getMessage()]);
    if (isset($conn)) $conn->close();
}
?>
