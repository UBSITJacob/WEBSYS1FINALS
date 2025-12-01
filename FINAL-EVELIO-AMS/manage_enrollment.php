<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
include "dbconfig.php";
$students = $pdo->query("SELECT id, CONCAT(family_name, ', ', first_name) AS name FROM students ORDER BY family_name")->fetchAll();
if(isset($_GET['student_id'])){
    $sid = (int)$_GET['student_id'];
    $crud = new pdoCRUD();
    $enrollments = $crud->getEnrollmentsByStudent($sid);
    $loads = $pdo->query("SELECT sl.id, CONCAT(s.code,' - ',s.name,' (',sec.name,')') AS label FROM subject_loads sl JOIN subjects s ON sl.subject_id=s.id JOIN sections sec ON sl.section_id=sec.id ORDER BY s.name")->fetchAll();
}
if(isset($_POST['add'])){
    $sid = (int)$_POST['student_id'];
    $lid = (int)$_POST['subject_load_id'];
    $sy = trim($_POST['school_year']);
    $sem = $_POST['semester'] ?? null;
    $crud = new pdoCRUD();
    $ok = $crud->addEnrollment($sid,$lid,$sy,$sem);
    $msg = $ok? 'Enrolled' : 'Failed';
}
if(isset($_POST['remove'])){
    $eid = (int)$_POST['enrollment_id'];
    $crud = new pdoCRUD();
    $ok = $crud->deleteEnrollment($eid);
    $msg = $ok? 'Removed' : 'Failed';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Enrollment</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:1000px;margin:20px auto;} table{width:100%;border-collapse:collapse;margin-top:10px;} th,td{border:1px solid #ddd;padding:8px;}</style>
</head>
<body>
<div class="container">
    <h3>Manage Enrollment</h3>
    <?php if(isset($msg)){ echo '<div>'.htmlspecialchars($msg,ENT_QUOTES,'UTF-8').'</div>'; } ?>
    <form method="get">
        <label>Student</label>
        <select name="student_id" required><?php foreach($students as $s){ echo '<option value="'.$s['id'].'"'.(isset($sid)&&$sid==$s['id']?' selected':'').'>'.htmlspecialchars($s['name'],ENT_QUOTES,'UTF-8').'</option>'; } ?></select>
        <button>Load</button>
    </form>
    <?php if(isset($sid)){ ?>
        <h4>Current Enrollments</h4>
        <table>
            <tr><th>Subject</th><th>Section</th><th>SY</th><th>Sem</th><th>Action</th></tr>
            <?php if(!$enrollments){ echo '<tr><td colspan="5" align="center">None</td></tr>'; } else { foreach($enrollments as $e){ echo '<tr><td>'.htmlspecialchars($e['subject_name'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($e['section_name'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($e['school_year'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($e['semester']??'',ENT_QUOTES,'UTF-8').'</td><td><form method="post"><input type="hidden" name="enrollment_id" value="'.$e['id'].'"><button name="remove">Remove</button></form></td></tr>'; } } ?>
        </table>
        <h4>Add Enrollment</h4>
        <form method="post">
            <input type="hidden" name="student_id" value="<?php echo (int)$sid; ?>">
            <label>Subject Load</label>
            <select name="subject_load_id" required><?php foreach($loads as $l){ echo '<option value="'.$l['id'].'">'.htmlspecialchars($l['label'],ENT_QUOTES,'UTF-8').'</option>'; } ?></select>
            <label>School Year</label>
            <input type="text" name="school_year" placeholder="2025-2026" required>
            <label>Semester</label>
            <select name="semester"><option value="">None</option><option>First</option><option>Second</option></select>
            <button name="add">Add Enrollment</button>
        </form>
    <?php } ?>
    <p><a href="admin_dashboard.php">Back</a></p>
</div>
</body>
</html>

