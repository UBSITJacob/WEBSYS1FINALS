<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ http_response_code(401); echo json_encode(array('success'=>false, 'error'=>'Unauthorized')); exit; }
$sid = (int)($_POST['student_id'] ?? 0);
$lid = (int)($_POST['subject_load_id'] ?? 0);
$date = trim($_POST['date'] ?? '');
$status = strtolower(trim($_POST['status'] ?? 'present'));
$allowed = ['present','absent','tardy'];
if(!in_array($status,$allowed)){ http_response_code(400); echo json_encode(array('success'=>false, 'error'=>'Invalid status')); exit; }
if($sid<1 || $lid<1 || $date===''){ http_response_code(400); echo json_encode(array('success'=>false, 'error'=>'Invalid payload')); exit; }
$crud = new pdoCRUD();
try{
    $ok = $crud->markAttendance($sid,$lid,$date,$status);
    if($ok){ http_response_code(200); echo json_encode(array('success'=>true)); }
    else { http_response_code(500); echo json_encode(array('success'=>false, 'error'=>'Save attendance failed')); }
}catch(Exception $e){ http_response_code(500); echo json_encode(array('success'=>false, 'error'=>$e->getMessage())); }
?>
