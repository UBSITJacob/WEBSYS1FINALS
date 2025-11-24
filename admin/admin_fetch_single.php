<?php
require_once "../includes/oop_functions.php";
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    echo json_encode(['status'=>'error','message'=>'Missing id']);
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id']);
if ($id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Invalid id']);
    exit;
}

$db = new Database();
$conn = $db->conn;

try {
    $sql = "SELECT u.id, u.fullname, u.username, u.email, ad.admin_id, ad.gender, ad.status, u.user_type
            FROM users u
            INNER JOIN admin_details ad ON u.id = ad.user_id
            WHERE u.id = ?
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();
        echo json_encode(['status'=>'success','admin'=>$row]);
    } else {
        echo json_encode(['status'=>'error','message'=>'Admin not found']);
    }
    $stmt->close();
} catch (Throwable $e) {
    echo json_encode(['status'=>'error','message'=>'Server error: '.$e->getMessage()]);
} finally {
    $conn->close();
}
?>
