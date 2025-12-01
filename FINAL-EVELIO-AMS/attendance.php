<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ header('Location: index.php'); exit; }
$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$loads = $crud->getTeacherLoads($acc['person_id']);
$selected = (int)($_GET['load_id'] ?? 0);
$date = $_GET['date'] ?? date('Y-m-d');
$students = $selected? $crud->getEnrollmentsByLoad($selected) : [];
if(isset($_POST['mark'])){
    $eid = (int)($_POST['enrollment_id'] ?? 0);
    $status = $_POST['status'] ?? 'present';
    foreach($students as $s){ if($s['enrollment_id']==$eid){ $crud->markAttendance($s['id'],$selected,$date,$status); break; } }
    $msg = 'Saved';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:1000px;margin:20px auto;} table{width:100%;border-collapse:collapse;margin-top:10px;} th,td{border:1px solid #ddd;padding:8px;}</style>
    <script>
        function selectLoad(){ const id = document.getElementById('load').value; const d = document.getElementById('date').value; window.location.href='attendance.php?load_id='+id+'&date='+encodeURIComponent(d); }
    </script>
</head>
<body>
<div class="container">
    <h3>Attendance</h3>
    <?php if(isset($msg)){ echo '<div>'.htmlspecialchars($msg,ENT_QUOTES,'UTF-8').'</div>'; } ?>
    <label>Subject Load</label>
    <select id="load" onchange="selectLoad()">
        <option value="0">Select load</option>
        <?php foreach($loads as $l){ $label = htmlspecialchars(($l['subject_code']??'').' - '.($l['subject_name']??'').' ('.($l['section_name']??'').')',ENT_QUOTES,'UTF-8'); echo '<option value="'.$l['id'].'"'.($selected==$l['id']?' selected':'').'>'.$label.'</option>'; } ?>
    </select>
    <label>Date</label>
    <input id="date" type="date" value="<?php echo htmlspecialchars($date,ENT_QUOTES,'UTF-8'); ?>" onchange="selectLoad()">
    <?php if($selected){ ?>
        <table>
            <tr><th>No.</th><th>Student</th><th>Mark</th></tr>
            <?php if(!$students){ echo '<tr><td colspan="3" align="center">No students enrolled</td></tr>'; } else { $i=1; foreach($students as $s){ echo '<tr><td>'.$i++.'</td><td>'.htmlspecialchars($s['family_name'].', '.$s['first_name'],ENT_QUOTES,'UTF-8').'</td><td><form method="post"><input type="hidden" name="enrollment_id" value="'.$s['enrollment_id'].'"><select name="status"><option>present</option><option>absent</option><option>tardy</option></select><button name="mark">Save</button></form></td></tr>'; } } ?>
        </table>
    <?php } ?>
    <p><a href="teacher_dashboard.php">Back</a></p>
</div>
</body>
</html>

