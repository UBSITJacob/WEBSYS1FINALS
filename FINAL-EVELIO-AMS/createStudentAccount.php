<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ echo json_encode(['success'=>false]); exit; }
$id = (int)($_POST['id'] ?? 0);
if($id<1){ echo json_encode(['success'=>false]); exit; }
$pdo = new pdoCRUD();
$ok = $pdo->createStudentAccount($id);
echo json_encode(['success'=>$ok]);
?>

