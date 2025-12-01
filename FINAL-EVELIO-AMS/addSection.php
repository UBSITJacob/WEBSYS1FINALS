<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ echo json_encode(['success'=>false]); exit; }
$name = trim($_POST['name'] ?? '');
$department = $_POST['department'] ?? '';
$grade = $_POST['grade_level'] ?? '';
$strand = $_POST['strand'] ?? null;
$capacity = (int)($_POST['capacity'] ?? 40);
if($name===''){ echo json_encode(['success'=>false]); exit; }
$pdo = new pdoCRUD();
$id = $pdo->addSection($name,$department,$grade,$strand,$capacity);
echo json_encode(['success'=> (bool)$id, 'id'=>$id]);
?>

