<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='student'){ header('Location: index.php'); exit; }
$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$sid = $acc['person_id'];
$year = (int)($_GET['year'] ?? date('Y'));
$month = (int)($_GET['month'] ?? date('n'));
$rows = $crud->getAttendanceByStudent($sid,$year,$month);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:900px;margin:20px auto;} table{width:100%;border-collapse:collapse;margin-top:10px;} th,td{border:1px solid #ddd;padding:8px;}</style>
</head>
<body>
<div class="container">
    <h3>Attendance</h3>
    <form method="get">
        <label>Year</label>
        <input type="number" name="year" value="<?php echo (int)$year; ?>" required>
        <label>Month</label>
        <input type="number" name="month" min="1" max="12" value="<?php echo (int)$month; ?>" required>
        <button>Load</button>
    </form>
    <table>
        <tr><th>Date</th><th>Subject</th><th>Status</th></tr>
        <?php if(!$rows){ echo '<tr><td colspan="3" align="center">None</td></tr>'; } else { foreach($rows as $r){ echo '<tr><td>'.htmlspecialchars($r['date'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($r['subject_name'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($r['status'],ENT_QUOTES,'UTF-8').'</td></tr>'; } } ?>
    </table>
    <p><a href="student_dashboard.php">Back</a></p>
</div>
</body>
</html>

