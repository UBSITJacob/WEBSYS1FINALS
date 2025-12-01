<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ echo json_encode(['success'=>false]); exit; }
$sid = (int)($_POST['student_id'] ?? 0);
$lid = (int)($_POST['subject_load_id'] ?? 0);
$date = trim($_POST['date'] ?? '');
$status = trim($_POST['status'] ?? 'present');
if($sid<1 || $lid<1 || $date===''){ echo json_encode(['success'=>false]); exit; }
$crud = new pdoCRUD();
$ok = $crud->markAttendance($sid,$lid,$date,$status);
echo json_encode(['success'=>$ok]);
?>

