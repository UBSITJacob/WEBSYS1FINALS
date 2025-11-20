<?php
session_start();
require_once "oop_functions.php";

// LOGIN CHECK
if (!isset($_SESSION['student'])) {
    header("Location: ../index.php");
    exit;
}

$student_id = $_SESSION['student']['id'];
$student = new Student($student_id);
$profile = $student->getProfile();

// Default profile if not found
if (!$profile) {
    $profile = [
        'fullname' => 'Unknown Student',
        'username' => '',
        'email' => '',
        'section' => 'N/A',
        'grade_level' => 'N/A',
    ];
}

$totalCourses = $student->totalCourses() ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
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
.card { background:#fff; padding:20px; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); text-align:center; }
.dashboard-cards { display:flex; gap:20px; flex-wrap:wrap; margin-top:20px; }
.card .count { font-size:2rem; font-weight:bold; color:#007bff; margin-top:10px; }
#toggleSidebar { background:#007bff; border:none; color:#fff; padding:8px 12px; border-radius:6px; cursor:pointer; font-weight:bold; }
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="profile-section">
        <img src="img/default.jpg" alt="Profile">
        <p><?= htmlspecialchars($profile['fullname']) ?></p>
    </div>
    <ul>
        <li><a class="active" href="index.php">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="grades.php">Grades</a></li>
        <li><a href="enrollment.php">Enrollment</a></li>
    </ul>
</div>

<div class="header" id="header">
    <div class="title">Student Dashboard</div>
    <div>
        <button id="toggleSidebar">☰</button>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="main-content" id="mainContent">
    <h2>Welcome, <?= htmlspecialchars($profile['fullname']) ?>!</h2>

    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Subject</h3>
            <div class="count"><?= $totalCourses ?></div>
        </div>
        <div class="card">
            <h3>Section</h3>
            <div class="count"><?= htmlspecialchars($profile['section']) ?></div>
        </div>
        <div class="card">
            <h3>Grade Level</h3>
            <div class="count"><?= htmlspecialchars($profile['grade_level']) ?></div>
        </div>
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
