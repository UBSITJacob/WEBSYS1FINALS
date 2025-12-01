<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ echo json_encode(['success'=>false]); exit; }
$eid = (int)($_POST['enrollment_id'] ?? 0);
$grade = trim($_POST['grade'] ?? '');
if($eid<1 || $grade===''){ echo json_encode(['success'=>false]); exit; }
$crud = new pdoCRUD();
$ok = $crud->upsertGrade($eid,$grade);
echo json_encode(['success'=>$ok]);
?>

