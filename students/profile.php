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
        'birthplace' => 'N/A',
        'religion' => 'N/A',
        'gender' => 'N/A',
        'mother_tongue' => 'N/A',
        'nationality' => 'N/A',
        'mobile' => 'N/A',
        'current_address' => 'N/A',
        'permanent_address' => 'N/A',
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
        /* Global Styles */
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
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
            transform: translateX(-230px);
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

        /* Profile Section Styling */
        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-section img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .profile-section p {
            margin-top: 10px;
            font-size: 1.2rem;
            color: #fff;
            font-weight: bold;
        }

        /* Sidebar settings button */
        .update-password-btn {
            background: #28a745;
            color: #fff;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            font-weight: bold;
            width: 100%;
        }

        .update-password-btn:hover {
            background: #218838;
        }

        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 230px;
            right: 0;
            height: 60px;
            background: #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            border-bottom: 2px solid #ccc;
            z-index: 999;
            transition: left 0.3s ease;
        }

        .header.sidebar-hidden {
            left: 0;
        }

        .header .title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header button {
            background: #007bff;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .header button:hover {
            background: #0056b3;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        /* Main Content */
        .main-content {
            margin-left: 230px;
            padding: 90px 25px 25px;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-hidden {
            margin-left: 0;
        }

        /* Profile Info Card Styling */
        .card {
            background: #fff;
            padding: 20px;
            min-width: 250px;
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
        <!-- Settings button -->
        <button class="update-password-btn" id="openSettings">Settings</button>
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
    <div class="controls">
        <button id="toggleSidebar">☰</button>
        <a href="logout.php" class="btn logout-btn">Logout</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">

    <!-- Profile Info Cards -->
    <div class="card">
        <h4>Basic Info</h4>
        <table class="table table-bordered">
            <tr><th>Full Name</th><td><?= htmlspecialchars($profile['fullname']) ?></td></tr>
            <tr><th>Username</th><td><?= isset($profile['username']) ? htmlspecialchars($profile['username']) : 'N/A' ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($profile['email']) ?></td></tr>
            <tr><th>Section</th><td><?= isset($profile['section']) ? htmlspecialchars($profile['section']) : 'N/A' ?></td></tr>
            <tr><th>Grade Level</th><td><?= isset($profile['grade_level']) ? htmlspecialchars($profile['grade_level']) : 'N/A' ?></td></tr>
            <tr><th>Total Subjects</th><td><?= $totalCourses ?></td></tr>
        </table>
    </div>

    <div class="card">
        <h4>Personal Information</h4>
        <table class="table table-bordered">
            <tr><th>Birthday</th><td><?= isset($profile['birthday']) ? htmlspecialchars($profile['birthday']) : 'N/A' ?></td></tr>
            <tr><th>Birthplace</th><td><?= isset($profile['birthplace']) ? htmlspecialchars($profile['birthplace']) : 'N/A' ?></td></tr>
            <tr><th>Religion</th><td><?= isset($profile['religion']) ? htmlspecialchars($profile['religion']) : 'N/A' ?></td></tr>
            <tr><th>Gender</th><td><?= isset($profile['gender']) ? htmlspecialchars($profile['gender']) : 'N/A' ?></td></tr>
            <tr><th>Mother Tongue</th><td><?= isset($profile['mother_tongue']) ? htmlspecialchars($profile['mother_tongue']) : 'N/A' ?></td></tr>
            <tr><th>Nationality</th><td><?= isset($profile['nationality']) ? htmlspecialchars($profile['nationality']) : 'N/A' ?></td></tr>
        </table>
    </div>

    <div class="card">
        <h4>Contact Information</h4>
        <table class="table table-bordered">
            <tr><th>Mobile Number</th><td><?= isset($profile['mobile']) ? htmlspecialchars($profile['mobile']) : 'N/A' ?></td></tr>
            <tr><th>Email Address</th><td><?= htmlspecialchars($profile['email']) ?></td></tr>
            <tr><th>Current Address</th><td><?= isset($profile['current_address']) ? htmlspecialchars($profile['current_address']) : 'N/A' ?></td></tr>
            <tr><th>Permanent Address</th><td><?= isset($profile['permanent_address']) ? htmlspecialchars($profile['permanent_address']) : 'N/A' ?></td></tr>
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

// Settings button click event
document.getElementById("openSettings").onclick = () => {
    window.location.href = "settings.php"; // Redirect to settings page
};
</script>

</body>
</html>
