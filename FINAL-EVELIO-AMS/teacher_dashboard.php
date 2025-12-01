<?php
include "session.php";
include "pdo_functions.php";
require_login();
if($_SESSION['role']!=='teacher'){ header('Location: index.php'); exit; }
$pdoC = new pdoCRUD();
$acc = $pdoC->getAccountById($_SESSION['account_id']);
$person = $pdoC->getAccountPerson('teacher',$acc['person_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teacher Dashboard</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:900px;margin:20px auto;}</style>
</head>
<body>
<div class="container">
    <h3>Welcome, <?php echo htmlspecialchars($person['full_name'],ENT_QUOTES,'UTF-8'); ?></h3>
    <p>Faculty ID: <?php echo htmlspecialchars($person['faculty_id'],ENT_QUOTES,'UTF-8'); ?></p>
    <p>Status: <?php echo $person['active']? 'Active':'Inactive'; ?></p>
    <p><a href="advisory_class.php">Advisory Class</a> | <a href="attendance.php">Attendance</a> | <a href="grades.php">Manage Grades</a></p>
</div>
</body>
</html>
