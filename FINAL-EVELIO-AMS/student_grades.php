<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='student'){ header('Location: index.php'); exit; }
$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$sid = $acc['person_id'];
$sy = $_GET['sy'] ?? '';
$sem = $_GET['sem'] ?? '';
$enrollments = [];
if($sy){
    $all = $crud->getEnrollmentsByStudent($sid);
    foreach($all as $e){ if($e['school_year']===$sy && ($sem==='' || ($e['semester']??'')===$sem)) $enrollments[]=$e; }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grades</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:900px;margin:20px auto;} table{width:100%;border-collapse:collapse;margin-top:10px;} th,td{border:1px solid #ddd;padding:8px;}</style>
</head>
<body>
<div class="container">
    <h3>Grades</h3>
    <form method="get">
        <label>School Year</label>
        <input type="text" name="sy" placeholder="2025-2026" value="<?php echo htmlspecialchars($sy,ENT_QUOTES,'UTF-8'); ?>" required>
        <label>Semester (SHS)</label>
        <select name="sem"><option value="">None</option><option <?php echo $sem==='First'?'selected':''; ?>>First</option><option <?php echo $sem==='Second'?'selected':''; ?>>Second</option></select>
        <button>Load</button>
    </form>
    <?php if($sy){ ?>
    <table>
        <tr><th>Subject</th><th>Section</th><th>Grade</th></tr>
        <?php if(!$enrollments){ echo '<tr><td colspan="3" align="center">None</td></tr>'; } else { foreach($enrollments as $e){
            $gSel = new PDO('mysql:host=localhost;dbname=evelio_ams_db;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
            $gs = $gSel->prepare('SELECT grade FROM grades WHERE enrollment_id = :id');
            $gs->execute([':id'=>$e['id']]);
            $g = $gs->fetch();
            echo '<tr><td>'.htmlspecialchars($e['subject_name'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($e['section_name'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars(($g['grade']??'-'),ENT_QUOTES,'UTF-8').'</td></tr>'; }
        } ?>
    </table>
    <?php } ?>
    <p><a href="student_dashboard.php">Back</a></p>
</div>
</body>
</html>

