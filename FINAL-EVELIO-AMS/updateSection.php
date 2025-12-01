<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ echo json_encode(['success'=>false]); exit; }
$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$capacity = (int)($_POST['capacity'] ?? 40);
if($id<1 || $name===''){ echo json_encode(['success'=>false]); exit; }
$pdo = new pdoCRUD();
// fetch existing to preserve fields
$curr = (function($pdo,$id){
    $s = new PDO("mysql:host=localhost;dbname=evelio_ams_db;charset=utf8mb4","root","",[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ]);
    $q = $s->prepare("SELECT department,grade_level,strand FROM sections WHERE id=:id");
    $q->execute([':id'=>$id]);
    return $q->fetch();
})($pdo,$id);
if(!$curr){ echo json_encode(['success'=>false]); exit; }
$ok = $pdo->updateSection($id,$name,$curr['department'],$curr['grade_level'],$curr['strand'],$capacity);
echo json_encode(['success'=>$ok]);
?>
