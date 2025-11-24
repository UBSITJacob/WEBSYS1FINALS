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
        'username' => 'N/A',
        'email' => 'N/A',
        'section' => 'N/A',
        'grade_level' => 'N/A',
    ];
}

$totalCourses = $student->totalCourses() ?? 0;

// Set the correct time zone (adjust this as needed for your region)
date_default_timezone_set('Asia/Manila');  // Set to Manila time zone or your local time zone

// Get current time for personalized greeting
$hour = date('H'); // Get the current hour
if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Global styles */
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
        }

        /* Sidebar styles */
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

        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin: 15px 0; }
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

        /* Profile Section */
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
            margin: auto;
        }

        .profile-section p {
            margin-top: 10px;
            font-size: 1.2rem;
            color: #fff;
            font-weight: bold;
        }

        /* Header styles */
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

        /* Main content */
        .main-content {
            margin-left: 230px;
            padding: 90px 25px 25px;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-hidden {
            margin-left: 0;
        }

        .dashboard-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .card {
            background: #fff;
            padding: 20px;
            min-width: 250px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card .count {
            font-size: 2rem;
            color: #007bff;
            font-weight: bold;
        }

        /* Animation for the greeting message */
        .greeting-message {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d47a1;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-in-out;
        }

        /* Keyframes for fade-in animation */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
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
        <li><a class="active" href="index.php">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="grades.php">Grades</a></li>
        <li><a href="enrollment.php">Enrollment</a></li>
    </ul>
</div>

<!-- HEADER -->
<div class="header" id="header">
    <div class="title">Student Dashboard</div>

    <div class="controls">
        <button id="toggleSidebar">☰</button>
        <a href="logout.php" class="btn logout-btn">Logout</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">

    <!-- Greeting Message -->
    <div class="greeting-message">
        <?= $greeting ?>, <?= htmlspecialchars($profile['fullname']) ?>!
    </div>

    <!-- Dashboard Cards -->
    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Subjects</h3>
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
// Sidebar toggle
document.getElementById("toggleSidebar").onclick = () => {
    document.getElementById("sidebar").classList.toggle("hidden");
    document.getElementById("header").classList.toggle("sidebar-hidden");
    document.getElementById("mainContent").classList.toggle("sidebar-hidden");
};
</script>

</body>
</html>