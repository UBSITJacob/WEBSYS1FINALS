<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='student'){ header('Location: index.php'); exit; }
$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$s = $crud->getAccountPerson('student',$acc['person_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:900px;margin:20px auto;} table{width:100%;border-collapse:collapse;} td{border:1px solid #ddd;padding:8px;}</style>
</head>
<body>
<div class="container">
    <h3>Profile</h3>
    <table>
        <tr><td>LRN</td><td><?php echo htmlspecialchars($s['lrn'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Name</td><td><?php echo htmlspecialchars($s['family_name'].', '.$s['first_name'].' '.$s['middle_name'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Department</td><td><?php echo htmlspecialchars($s['department'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Grade Level</td><td><?php echo htmlspecialchars($s['grade_level'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Strand</td><td><?php echo htmlspecialchars($s['strand']??'',ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Current Address</td><td><?php echo htmlspecialchars($s['curr_house_street'].' '.$s['curr_barangay'].' '.$s['curr_city'].' '.$s['curr_province'].' '.$s['curr_zip'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Permanent Address</td><td><?php echo htmlspecialchars($s['perm_house_street'].' '.$s['perm_barangay'].' '.$s['perm_city'].' '.$s['perm_province'].' '.$s['perm_zip'],ENT_QUOTES,'UTF-8'); ?></td></tr>
    </table>
    <p><a href="student_dashboard.php">Back</a></p>
</div>
</body>
</html>

