<?php
session_start();
require_once "oop_functions.php";

if (!isset($_SESSION['student'])) {
    header("Location: ../index.php");
    exit;
}

$student_id = $_SESSION['student']['id'];
$student = new Student($student_id);
// $enrollments now correctly fetches subjects linked to the section
$enrollments = $student->getEnrollment() ?: [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Enrollment | Student Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { margin:0; font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; }
.sidebar { background-color: #1a1a1a; color:white; width:230px; height:100vh; position:fixed; top:0; left:0; padding:20px; transition:0.3s; }
.sidebar ul { list-style:none; padding:0; }
.sidebar ul li { margin:15px 0; }
.sidebar ul li a { text-decoration:none; color:#ccc; font-weight:600; display:block; padding:8px 10px; border-radius:5px; }
.sidebar ul li a:hover, .sidebar ul li a.active { background-color: #007bff; color:#fff; }
.profile-section { text-align:center; margin-bottom:20px; }
.profile-section img { width:100px; height:100px; border-radius:50%; object-fit:cover; margin:0 auto; }
.profile-section p { margin:10px 0 5px; font-weight:bold; color:#fff; }
.header { margin-left:230px; background:#e9ecef; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #ccc; transition:0.3s; }
.main-content { margin-left:230px; padding:25px; min-height:calc(100vh - 60px); transition:0.3s; }
.card { background:#fff; padding:20px; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); text-align:left; margin-bottom:20px; }
.card table { width:100%; }
.card table th, .card table td { text-align:left; padding:8px; width:auto; } /* Adjusted width */
#toggleSidebar { background:#007bff; border:none; color:#fff; padding:8px 12px; border-radius:6px; cursor:pointer; font-weight:bold; }
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="profile-section">
        <img src="img/default.jpg" alt="Profile">
        <p><?= htmlspecialchars($_SESSION['student']['fullname'] ?? 'Student') ?></p>
    </div>
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="grades.php">Grades</a></li>
        <li><a class="active" href="enrollment.php">Enrollment</a></li>
    </ul>
</div>

<div class="header" id="header">
    <div class="title">Enrollment</div>
    <div>
        <button id="toggleSidebar">☰</button>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="main-content" id="mainContent">
    <div class="card">
        <h3>Your Subjects (Current Load)</h3>
        <?php if (!empty($enrollments)): ?>
        <table class="table table-bordered table-striped">
            <tr><th>Subject Name</th><th>Subject Code</th></tr> 
            <?php foreach ($enrollments as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['subject_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($e['subject_code'] ?? 'N/A') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p class="text-center">You are not enrolled in any subjects yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const header = document.getElementById('header');
const mainContent = document.getElementById('mainContent');
document.getElementById('toggleSidebar').onclick = () => {
    sidebar.classList.toggle('hidden');
    header.style.marginLeft = sidebar.classList.contains('hidden') ? '0' : '230px';
    mainContent.style.marginLeft = sidebar.classList.contains('hidden') ? '0' : '230px';
};
</script>
</body>
</html>