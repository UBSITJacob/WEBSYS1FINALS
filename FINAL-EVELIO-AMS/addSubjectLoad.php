<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ echo json_encode(['success'=>false]); exit; }
$teacher_id = (int)($_POST['teacher_id'] ?? 0);
$subject_id = (int)($_POST['subject_id'] ?? 0);
$section_id = (int)($_POST['section_id'] ?? 0);
$school_year = trim($_POST['school_year'] ?? '');
$semester = $_POST['semester'] ?? null;
if($teacher_id<1||$subject_id<1||$section_id<1||$school_year===''){ echo json_encode(['success'=>false]); exit; }
$pdo = new pdoCRUD();
$id = $pdo->addSubjectLoad($teacher_id,$subject_id,$section_id,$school_year,$semester);
echo json_encode(['success'=> (bool)$id, 'id'=>$id]);
?>

