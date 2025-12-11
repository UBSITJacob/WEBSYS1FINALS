<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ echo json_encode(['success'=>false]); exit; }
$eid = (int)($_POST['enrollment_id'] ?? 0);
$grade = trim($_POST['grade'] ?? '');
$q1 = isset($_POST['q1']) ? trim($_POST['q1']) : null;
$q2 = isset($_POST['q2']) ? trim($_POST['q2']) : null;
$q3 = isset($_POST['q3']) ? trim($_POST['q3']) : null;
$q4 = isset($_POST['q4']) ? trim($_POST['q4']) : null;
if($eid<1 || $grade===''){ echo json_encode(['success'=>false]); exit; }
$crud = new pdoCRUD();
$ok = $crud->upsertGrade($eid,$grade,$q1,$q2,$q3,$q4);
echo json_encode(['success'=>$ok]);
?>
