<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ echo json_encode(['success'=>false]); exit; }
$faculty_id = trim($_POST['faculty_id'] ?? '');
$full_name = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$sex = $_POST['sex'] ?? 'Male';
$email = trim($_POST['email'] ?? '');
if($faculty_id===''||$full_name===''||$username===''||$email===''){ echo json_encode(['success'=>false]); exit; }
$pdo = new pdoCRUD();
$id = $pdo->addTeacher($faculty_id,$full_name,$username,$sex,$email);
echo json_encode(['success'=> (bool)$id, 'id'=>$id]);
?>

