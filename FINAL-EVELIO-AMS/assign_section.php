<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
include "dbconfig.php";
$students = $pdo->query("SELECT id, CONCAT(family_name, ', ', first_name) AS name FROM students ORDER BY family_name")->fetchAll();
$sections = $pdo->query("SELECT id, name FROM sections ORDER BY name")->fetchAll();
if(isset($_POST['assign'])){
    $sid = (int)($_POST['student_id'] ?? 0);
    $sec = (int)($_POST['section_id'] ?? 0);
    $crud = new pdoCRUD();
    $ok = $crud->assignStudentSection($sid,$sec);
    $msg = $ok? 'Assigned' : 'Section full or invalid';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assign Section</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:700px;margin:20px auto;} select{width:100%;padding:8px;} button{padding:10px 14px;margin-top:10px;}</style>
</head>
<body>
<div class="container">
    <h3>Assign Section</h3>
    <?php if(isset($msg)){ echo '<div>'.htmlspecialchars($msg,ENT_QUOTES,'UTF-8').'</div>'; } ?>
    <form method="post">
        <label>Student</label>
        <select name="student_id" required><?php foreach($students as $s){ echo '<option value="'.$s['id'].'">'.htmlspecialchars($s['name'],ENT_QUOTES,'UTF-8').'</option>'; } ?></select>
        <label>Section</label>
        <select name="section_id" required><?php foreach($sections as $sec){ echo '<option value="'.$sec['id'].'">'.htmlspecialchars($sec['name'],ENT_QUOTES,'UTF-8').'</option>'; } ?></select>
        <button name="assign">Assign Section</button>
    </form>
    <p><a href="admin_dashboard.php">Back</a></p>
    
</div>
</body>
</html>

