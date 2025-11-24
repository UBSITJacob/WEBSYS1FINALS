<?php
session_start();
require_once "oop_functions.php";

if (!isset($_SESSION['student'])) {
    header("Location: ../index.php");
    exit;
}

$student_id = $_SESSION['student']['id'];
$student = new Student($student_id);
$profile = $student->getProfile();

// Default values if profile not found
if (!$profile) {
    $profile = [
        'fullname' => 'Unknown Student',
        'username' => 'N/A',
        'email' => 'N/A',
        'section' => 'N/A',
        'grade_level' => 'N/A',
        'birthday' => 'N/A',
        'gender' => 'N/A',
        'mobile' => 'N/A',
    ];
}

$totalCourses = $student->totalCourses() ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profile | Student Portal</title>
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
.card h4 { border-bottom:1px solid #ddd; padding-bottom:5px; margin-bottom:15px; color:#333; }
.card table { width:100%; }
.card table th, .card table td { text-align:left; padding:8px; width:50%; }
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
        <li><a href="index.php">Dashboard</a></li>
        <li><a class="active" href="profile.php">Profile</a></li>
        <li><a href="grades.php">Grades</a></li>
        <li><a href="enrollment.php">Enrollment</a></li>
    </ul>
</div>

<div class="header" id="header">
    <div class="title">Profile</div>
    <div>
        <button id="toggleSidebar">☰</button>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="main-content" id="mainContent">

    <div class="card">
        <h4>Basic Info</h4>
        <table class="table table-bordered">
            <tr><th>Full Name</th><td><?= htmlspecialchars($profile['fullname']) ?></td></tr>
            <tr><th>Username</th><td><?= htmlspecialchars($profile['username']) ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($profile['email']) ?></td></tr>
            <tr><th>Section</th><td><?= htmlspecialchars($profile['section']) ?></td></tr>
            <tr><th>Grade Level</th><td><?= htmlspecialchars($profile['grade_level']) ?></td></tr>
            <tr><th>Total Subjects</th><td><?= $totalCourses ?></td></tr>
        </table>
    </div>

    <div class="card">
        <h4>Personal Information</h4>
        <table class="table table-bordered">
            <tr><th>Birthday</th><td><?= htmlspecialchars($profile['birthday']) ?></td></tr>
            <tr><th>Gender</th><td><?= htmlspecialchars($profile['gender']) ?></td></tr>
        </table>
    </div>

    <div class="card">
        <h4>Contact Information</h4>
        <table class="table table-bordered">
            <tr><th>Mobile Number</th><td><?= htmlspecialchars($profile['mobile']) ?></td></tr>
            <tr><th>E-mail Address</th><td><?= htmlspecialchars($profile['email']) ?></td></tr>
        </table>
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