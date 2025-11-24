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
$currentPage = basename($_SERVER['PHP_SELF']);  // Get current page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: #1a1a1a;
            color: white;
            width: 230px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-230px); /* Hide the sidebar */
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #ccc;
            font-weight: 600;
            display: block;
            padding: 8px 10px;
            border-radius: 5px;
        }

        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: #007bff;
            color: #fff;
        }

        /* Header Styles */
        .header {
            margin-left: 230px;
            background: #e9ecef;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ccc;
            transition: 0.3s;
        }

        .header.sidebar-hidden {
            margin-left: 0;
        }

        .main-content {
            margin-left: 230px;
            padding: 25px;
            min-height: calc(100vh - 60px);
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-hidden {
            margin-left: 0;
        }

        /* Profile Section Styles */
        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Reduced size for profile image */
        .profile-section img {
            width: 90px; /* Smaller image */
            height: 90px; /* Smaller image */
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Added shadow to profile image */
        }

        .profile-section p { 
            margin-top: 10px;
            font-size: 1.1rem; /* Slightly smaller font size */
            color: #333;
            font-weight: bold;
        }

        /* Card Styles */
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card h4 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .card table {
            width: 100%;
        }

        .card table th, .card table td {
            text-align: left;
            padding: 10px;
        }

        /* Sidebar and Link Hover Effects */
        .sidebar ul li a:hover {
            background-color: #0056b3;
            color: #fff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }

            .header {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="profile-section">
        <img src="https://cdn-icons-png.flaticon.com/512/3870/3870822.png" alt="Profile pic">
        <p><?= htmlspecialchars($profile['fullname']) ?></p>
    </div>
    <ul>
        <li><a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="profile.php" class="<?= ($currentPage == 'profile.php') ? 'active' : '' ?>">Profile</a></li>
        <li><a href="grades.php" class="<?= ($currentPage == 'grades.php') ? 'active' : '' ?>">Grades</a></li>
        <li><a href="enrollment.php" class="<?= ($currentPage == 'enrollment.php') ? 'active' : '' ?>">Enrollment</a></li>
    </ul>
</div>

<!-- HEADER -->
<div class="header" id="header">
    <div class="title">Student Profile</div>
    <div>
        <button id="toggleSidebar" class="btn btn-primary">☰</button>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">

<<<<<<< HEAD
=======
    <!-- Profile Section -->
    <div class="profile-section">
        <img src="https://cdn-icons-png.flaticon.com/512/3870/3870822.png" alt="Profile pic">
        <p><?= htmlspecialchars($profile['fullname']) ?></p>
    </div>

    <!-- Basic Info Card -->
>>>>>>> 14ddbc2b81b98676118ba57323f360df6caaede0
    <div class="card">
        <h4>Basic Info</h4>
        <table class="table table-bordered">
            <tr><th>Full Name</th><td><?= htmlspecialchars($profile['fullname']) ?></td></tr>
            <tr><th>Username</th><td><?= isset($profile['username']) ? htmlspecialchars($profile['username']) : 'N/A' ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($profile['email']) ?></td></tr>
<<<<<<< HEAD
            <tr><th>Section</th><td><?= htmlspecialchars($profile['section']) ?></td></tr>
            <tr><th>Grade Level</th><td><?= htmlspecialchars($profile['grade_level']) ?></td></tr>
=======
            <tr><th>Section</th><td><?= isset($profile['section']) ? htmlspecialchars($profile['section']) : 'N/A' ?></td></tr>
            <tr><th>Grade Level</th><td><?= isset($profile['grade_level']) ? htmlspecialchars($profile['grade_level']) : 'N/A' ?></td></tr>
>>>>>>> 14ddbc2b81b98676118ba57323f360df6caaede0
            <tr><th>Total Subjects</th><td><?= $totalCourses ?></td></tr>
        </table>
    </div>

    <div class="card">
        <h4>Personal Information</h4>
        <table class="table table-bordered">
<<<<<<< HEAD
            <tr><th>Birthday</th><td><?= htmlspecialchars($profile['birthday']) ?></td></tr>
            <tr><th>Gender</th><td><?= htmlspecialchars($profile['gender']) ?></td></tr>
=======
            <tr><th>Birthday</th><td><?= isset($profile['birthday']) ? htmlspecialchars($profile['birthday']) : 'N/A' ?></td></tr>
            <tr><th>Birthplace</th><td><?= isset($profile['birthplace']) ? htmlspecialchars($profile['birthplace']) : 'N/A' ?></td></tr>
            <tr><th>Religion</th><td><?= isset($profile['religion']) ? htmlspecialchars($profile['religion']) : 'N/A' ?></td></tr>
            <tr><th>Gender</th><td><?= isset($profile['gender']) ? htmlspecialchars($profile['gender']) : 'N/A' ?></td></tr>
            <tr><th>Mother Tongue</th><td><?= isset($profile['mother_tongue']) ? htmlspecialchars($profile['mother_tongue']) : 'N/A' ?></td></tr>
            <tr><th>Nationality</th><td><?= isset($profile['nationality']) ? htmlspecialchars($profile['nationality']) : 'N/A' ?></td></tr>
>>>>>>> 14ddbc2b81b98676118ba57323f360df6caaede0
        </table>
    </div>

    <div class="card">
        <h4>Contact Information</h4>
        <table class="table table-bordered">
<<<<<<< HEAD
            <tr><th>Mobile Number</th><td><?= htmlspecialchars($profile['mobile']) ?></td></tr>
            <tr><th>E-mail Address</th><td><?= htmlspecialchars($profile['email']) ?></td></tr>
=======
            <tr><th>Mobile Number</th><td><?= isset($profile['mobile']) ? htmlspecialchars($profile['mobile']) : 'N/A' ?></td></tr>
            <tr><th>Email Address</th><td><?= htmlspecialchars($profile['email']) ?></td></tr>
            <tr><th>Current Address</th><td><?= isset($profile['current_address']) ? htmlspecialchars($profile['current_address']) : 'N/A' ?></td></tr>
            <tr><th>Permanent Address</th><td><?= isset($profile['permanent_address']) ? htmlspecialchars($profile['permanent_address']) : 'N/A' ?></td></tr>
>>>>>>> 14ddbc2b81b98676118ba57323f360df6caaede0
        </table>
    </div>

</div>

<script>
// Sidebar toggle
document.getElementById("toggleSidebar").onclick = () => {
    document.getElementById("sidebar").classList.toggle("hidden");
    document.getElementById("header").classList.toggle("sidebar-hidden");
    document.getElementById("mainContent").classList.toggle("sidebar-hidden");
};
</script>

</body>
</html>