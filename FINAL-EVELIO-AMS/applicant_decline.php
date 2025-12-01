<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ echo json_encode(['success'=>false]); exit; }
$id = (int)($_POST['id'] ?? 0);
$pdo = new pdoCRUD();
$ok = $id>0 ? $pdo->declineApplicant($id) : false;
echo json_encode(['success'=>$ok]);
?>

