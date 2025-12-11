<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ echo json_encode(['success'=>false]); exit; }
$id = (int)($_POST['id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
if($id<1){ echo json_encode(['success'=>false]); exit; }
$pdo = new pdoCRUD();
$ok = false;
if($username !== ''){
    $ok = $pdo->updateStudentAccount($id,$username) || $ok;
}
if($new_password !== '' || $confirm_password !== ''){
    if($new_password !== $confirm_password){ echo json_encode(['success'=>false]); exit; }
    if(strlen($new_password) < 8){ echo json_encode(['success'=>false]); exit; }
    $ok = $pdo->setStudentPassword($id,$new_password) || $ok;
}
echo json_encode(['success'=>$ok]);
?>
