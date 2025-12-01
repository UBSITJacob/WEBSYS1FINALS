<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
$pdo = new pdoCRUD();
$s = $pdo->getStudentById($id);
if(!$s){ echo 'Not found'; exit; }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student View</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:900px;margin:20px auto;} table{width:100%;border-collapse:collapse;} td{border:1px solid #ddd;padding:8px;}</style>
</head>
<body>
<div class="container">
    <h3>Student Details</h3>
    <table>
        <tr><td>LRN</td><td><?php echo htmlspecialchars($s['lrn'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Department</td><td><?php echo htmlspecialchars($s['department'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Grade Level</td><td><?php echo htmlspecialchars($s['grade_level'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Strand</td><td><?php echo htmlspecialchars($s['strand'] ?? '',ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Student Type</td><td><?php echo htmlspecialchars($s['student_type'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Name</td><td><?php echo htmlspecialchars($s['family_name'].', '.$s['first_name'].' '.$s['middle_name'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Birthdate</td><td><?php echo htmlspecialchars($s['birthdate'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Birthplace</td><td><?php echo htmlspecialchars($s['birthplace'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Religion</td><td><?php echo htmlspecialchars($s['religion'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Civil Status</td><td><?php echo htmlspecialchars($s['civil_status'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Sex</td><td><?php echo htmlspecialchars($s['sex'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Mobile</td><td><?php echo htmlspecialchars($s['mobile'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Email</td><td><?php echo htmlspecialchars($s['email'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Current Address</td><td><?php echo htmlspecialchars($s['curr_house_street'].' '.$s['curr_barangay'].' '.$s['curr_city'].' '.$s['curr_province'].' '.$s['curr_zip'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Permanent Address</td><td><?php echo htmlspecialchars($s['perm_house_street'].' '.$s['perm_barangay'].' '.$s['perm_city'].' '.$s['perm_province'].' '.$s['perm_zip'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Elementary</td><td><?php echo htmlspecialchars(($s['elem_name']??'').' '.($s['elem_address']??'').' '.($s['elem_year_graduated']??''),ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Last School</td><td><?php echo htmlspecialchars(($s['last_school_name']??'').' '.($s['last_school_address']??''),ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Junior High</td><td><?php echo htmlspecialchars(($s['jhs_name']??'').' '.($s['jhs_address']??'').' '.($s['jhs_year_graduated']??''),ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Guardian</td><td><?php echo htmlspecialchars($s['guardian_last_name'].' '.$s['guardian_first_name'].' '.$s['guardian_middle_name'].' '.$s['guardian_contact'].' '.$s['guardian_occupation'].' '.$s['guardian_address'].' '.$s['guardian_relationship'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Mother</td><td><?php echo htmlspecialchars($s['mother_last_name'].' '.$s['mother_first_name'].' '.$s['mother_middle_name'].' '.$s['mother_contact'].' '.$s['mother_occupation'].' '.$s['mother_address'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Father</td><td><?php echo htmlspecialchars($s['father_last_name'].' '.$s['father_first_name'].' '.$s['father_middle_name'].' '.$s['father_contact'].' '.$s['father_occupation'].' '.$s['father_address'],ENT_QUOTES,'UTF-8'); ?></td></tr>
    </table>
    <p><a href="students.php">Back</a></p>
</div>
</body>
</html>

