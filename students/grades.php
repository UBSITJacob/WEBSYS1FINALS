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
$grades = $student->getGrades() ?: [];

// Define the required period keys for column headers
$periods = ['1st Qtr', '2nd Qtr', '3rd Qtr', '4th Qtr', 'Final'];

// Calculate average (Optional but good for display)
$overall_total = 0;
$overall_count = 0;

if (!empty($grades)) {
    foreach ($grades as $subject_data) {
        // Find the numerical grades for the subject
        $numerical_grades = array_filter($subject_data, function($key) {
            return in_array($key, ['1st Qtr', '2nd Qtr', '3rd Qtr', '4th Qtr', 'Final']);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($numerical_grades as $grade_value) {
            if (is_numeric($grade_value)) {
                $overall_total += (float)$grade_value;
                $overall_count++;
            }
        }
    }
}
$average = ($overall_count > 0) ? round($overall_total / $overall_count, 2) : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grades | Student Portal</title>
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

        /* Sidebar settings button */
        .update-password-btn {
            background: #28a745; /* Green button */
            color: #fff;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            font-weight: bold;
        }

        .update-password-btn:hover {
            background: #218838; /* Darker green when hovered */
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


        /* Keyframes for fade-in animation */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* Grade table styles */
        .grade-table-container {
            overflow-x: auto;
            margin-top: 15px;
        }

        .grade-table {
            width: 100%;
            min-width: 700px;
            border-collapse: collapse;
        }

        .grade-table th, .grade-table td {
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
        }

        .grade-table th {
            background-color: #007bff;
            color: white;
            border-color: #0056b3;
        }

        .grade-table th:first-child {
            text-align: left;
            background-color: #333;
        }

        .grade-table tbody td:first-child {
            text-align: left;
            font-weight: 500;
        }

        .grade-table tfoot td {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        /* Sidebar toggle button */
        #toggleSidebar {
            background: #007bff;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="profile-section">
        <img src="https://cdn-icons-png.flaticon.com/512/3870/3870822.png" alt="Profile pic">
        <p><?= htmlspecialchars($_SESSION['student']['fullname']) ?></p>
        <!-- Settings button -->
        <button class="update-password-btn" id="openSettings">Settings</button>
    </div>

    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a class="active" href="grades.php">Grades</a></li>
    </ul>
</div>

<!-- HEADER -->
<div class="header" id="header">
    <div class="title">Grades</div>

    <div class="controls">
        <button id="toggleSidebar">☰</button>
        <a href="logout.php" class="btn logout-btn">Logout</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">

    <div class="card">
        <h3>Grade Records (Current Academic Year)</h3>
        <?php if (!empty($grades)): ?>
        
        <div class="grade-table-container">
            <table class="grade-table table-striped">
                <thead>
                    <tr>
                        <th style="width: 30%;">SUBJECT</th>
                        <?php foreach ($periods as $p): ?>
                            <th><?= htmlspecialchars($p) ?></th>
                        <?php endforeach; ?>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grades as $g): ?>
                    <tr>
                        <td><?= htmlspecialchars($g['Subject']) ?></td>
                        <?php foreach ($periods as $p): ?>
                            <td><?= htmlspecialchars($g[$p] ?? '-') ?></td>
                        <?php endforeach; ?>
                        <td>PASSED</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Overall Average</td>
                        <td colspan="<?= count($periods) + 2 ?>">
                            <?= htmlspecialchars($average) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php else: ?>
        <p class="text-center">No grades available for your current enrollment.</p>
        <?php endif; ?>
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
