<?php
header('Content-Type: application/json');
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ echo json_encode([]); exit; }
$pdo = new pdoCRUD();
$rows = $pdo->getSections('',1,100,'name','ASC');
$out = array_map(function($r){ return ['id'=>(int)$r['id'],'name'=>$r['name'].' ('.$r['grade_level'].')']; }, $rows);
echo json_encode($out);
?>
