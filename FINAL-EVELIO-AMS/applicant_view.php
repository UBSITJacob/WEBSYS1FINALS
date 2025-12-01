<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
$pdo = new pdoCRUD();
$a = $pdo->getApplicantById($id);
if(!$a){ echo 'Not found'; exit; }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Applicant View</title>
    <style>
        body{font-family: Arial, sans-serif;}
        .container{max-width:900px;margin:20px auto;}
        table{width:100%;border-collapse:collapse;}
        td{border:1px solid #ddd;padding:8px;}
    </style>
</head>
<body>
<div class="container">
    <h3>Applicant Details</h3>
    <table>
        <tr><td>LRN</td><td><?php echo htmlspecialchars($a['lrn'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Department</td><td><?php echo htmlspecialchars($a['department'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Grade Level</td><td><?php echo htmlspecialchars($a['grade_level'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Strand</td><td><?php echo htmlspecialchars($a['strand'] ?? '',ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Student Type</td><td><?php echo htmlspecialchars($a['student_type'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Family Name</td><td><?php echo htmlspecialchars($a['family_name'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>First Name</td><td><?php echo htmlspecialchars($a['first_name'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Middle Name</td><td><?php echo htmlspecialchars($a['middle_name'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Birthdate</td><td><?php echo htmlspecialchars($a['birthdate'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Birthplace</td><td><?php echo htmlspecialchars($a['birthplace'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Religion</td><td><?php echo htmlspecialchars($a['religion'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Civil Status</td><td><?php echo htmlspecialchars($a['civil_status'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Sex</td><td><?php echo htmlspecialchars($a['sex'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Mobile</td><td><?php echo htmlspecialchars($a['mobile'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Email</td><td><?php echo htmlspecialchars($a['email'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Current Address</td><td><?php echo htmlspecialchars($a['curr_house_street'].' '.$a['curr_barangay'].' '.$a['curr_city'].' '.$a['curr_province'].' '.$a['curr_zip'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Permanent Address</td><td><?php echo htmlspecialchars($a['perm_house_street'].' '.$a['perm_barangay'].' '.$a['perm_city'].' '.$a['perm_province'].' '.$a['perm_zip'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Elementary</td><td><?php echo htmlspecialchars(($a['elem_name']??'').' '.($a['elem_address']??'').' '.($a['elem_year_graduated']??''),ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Last School</td><td><?php echo htmlspecialchars(($a['last_school_name']??'').' '.($a['last_school_address']??''),ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Junior High</td><td><?php echo htmlspecialchars(($a['jhs_name']??'').' '.($a['jhs_address']??'').' '.($a['jhs_year_graduated']??''),ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Guardian</td><td><?php echo htmlspecialchars($a['guardian_last_name'].' '.$a['guardian_first_name'].' '.$a['guardian_middle_name'].' '.$a['guardian_contact'].' '.$a['guardian_occupation'].' '.$a['guardian_address'].' '.$a['guardian_relationship'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Mother</td><td><?php echo htmlspecialchars($a['mother_last_name'].' '.$a['mother_first_name'].' '.$a['mother_middle_name'].' '.$a['mother_contact'].' '.$a['mother_occupation'].' '.$a['mother_address'],ENT_QUOTES,'UTF-8'); ?></td></tr>
        <tr><td>Father</td><td><?php echo htmlspecialchars($a['father_last_name'].' '.$a['father_first_name'].' '.$a['father_middle_name'].' '.$a['father_contact'].' '.$a['father_occupation'].' '.$a['father_address'],ENT_QUOTES,'UTF-8'); ?></td></tr>
    </table>
    <p><a href="applicants.php">Back</a></p>
</div>
</body>
</html>

