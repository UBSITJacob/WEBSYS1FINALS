<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ header('Location: index.php'); exit; }
$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$loads = $crud->getTeacherLoads($acc['person_id']);
$selected = (int)($_GET['load_id'] ?? 0);
$enrolled = $selected? $crud->getEnrollmentsByLoad($selected) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Grades</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:1000px;margin:20px auto;} table{width:100%;border-collapse:collapse;margin-top:10px;} th,td{border:1px solid #ddd;padding:8px;}</style>
    <script>
        function selectLoad(){ const id = document.getElementById('load').value; window.location.href='grades.php?load_id='+id; }
        function saveGrade(eid){ const g = prompt('Enter grade:'); if(g===null) return; fetch('saveGrade.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'enrollment_id='+eid+'&grade='+encodeURIComponent(g)}).then(r=>r.json()).then(j=>{ alert(j.success? 'Saved':'Failed'); }); }
    </script>
</head>
<body>
<div class="container">
    <h3>Manage Grades</h3>
    <label>Subject Load</label>
    <select id="load" onchange="selectLoad()">
        <option value="0">Select load</option>
        <?php foreach($loads as $l){ $label = htmlspecialchars(($l['subject_code']??'').' - '.($l['subject_name']??'').' ('.($l['section_name']??'').')',ENT_QUOTES,'UTF-8'); echo '<option value="'.$l['id'].'"'.($selected==$l['id']?' selected':'').'>'.$label.'</option>'; } ?>
    </select>
    <?php if($selected){ ?>
        <table>
            <tr><th>No.</th><th>Section</th><th>Subject Code</th><th>Subject Name</th><th>Student</th><th>Grade</th><th>Action</th></tr>
            <?php if(!$enrolled){ echo '<tr><td colspan="7" align="center">No students enrolled</td></tr>'; } else { $i=1; foreach($enrolled as $e){ echo '<tr><td>'.$i++.'</td><td>'.htmlspecialchars($l['section_name']??'',ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($l['subject_code']??'',ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($l['subject_name']??'',ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($e['family_name'].', '.$e['first_name'],ENT_QUOTES,'UTF-8').'</td><td>-</td><td><button onclick="saveGrade('.(int)$e['enrollment_id'].')">Save</button></td></tr>'; } } ?>
        </table>
    <?php } ?>
    <p><a href="teacher_dashboard.php">Back</a></p>
</div>
</body>
</html>

