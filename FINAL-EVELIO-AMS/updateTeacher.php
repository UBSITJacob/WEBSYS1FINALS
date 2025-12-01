<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ echo json_encode(['success'=>false]); exit; }
$id = (int)($_POST['id'] ?? 0);
$full_name = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$sex = $_POST['sex'] ?? 'Male';
$email = trim($_POST['email'] ?? '');
$active = (int)($_POST['active'] ?? 1);
if($id<1||$full_name===''||$username===''||$email===''){ echo json_encode(['success'=>false]); exit; }
$pdo = new pdoCRUD();
$ok = $pdo->updateTeacher($id,$full_name,$username,$sex,$email,$active);
echo json_encode(['success'=>$ok]);
?>

