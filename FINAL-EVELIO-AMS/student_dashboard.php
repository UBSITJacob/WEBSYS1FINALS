<?php
include "session.php";
include "pdo_functions.php";
require_login();
if($_SESSION['role']!=='student'){ header('Location: index.php'); exit; }
$pdoC = new pdoCRUD();
$acc = $pdoC->getAccountById($_SESSION['account_id']);
$person = $pdoC->getAccountPerson('student',$acc['person_id']);
include "dbconfig.php";
$total_subjects = (int)$pdo->prepare("SELECT COUNT(*) c FROM enrollments WHERE student_id = :sid");
$total_subjects->execute([':sid'=>$person['id']]);
$subjects_count = (int)$total_subjects->fetch()['c'];
$section = '';
if($person['advisory_section_id']){
    $s = $pdo->prepare("SELECT name FROM sections WHERE id = :id");
    $s->execute([':id'=>$person['advisory_section_id']]);
    $row = $s->fetch();
    $section = $row? $row['name'] : '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:900px;margin:20px auto;}</style>
</head>
<body>
<div class="container">
    <h3>Welcome, <?php echo htmlspecialchars($person['first_name'].' '.$person['family_name'],ENT_QUOTES,'UTF-8'); ?></h3>
    <p>Section: <?php echo htmlspecialchars($section,ENT_QUOTES,'UTF-8'); ?></p>
    <p>Grade Level: <?php echo htmlspecialchars($person['grade_level'],ENT_QUOTES,'UTF-8'); ?></p>
    <p>Total Subjects: <?php echo $subjects_count; ?></p>
    <p><a href="student_profile.php">Profile</a> | <a href="student_grades.php">Grades</a> | <a href="student_attendance.php">Attendance</a></p>
</div>
</body>
</html>

