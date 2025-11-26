<?php
session_start();
require_once "oop_functions.php";

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
.card { background:#fff; padding:20px; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); text-align:center; margin-bottom:20px; }
.grade-table-container { overflow-x: auto; margin-top: 15px; }
.grade-table { width: 100%; min-width: 700px; border-collapse: collapse; }
.grade-table th, .grade-table td { text-align:center; padding:8px; border: 1px solid #ddd; }
.grade-table th { background-color: #007bff; color: white; border-color: #0056b3; }
.grade-table th:first-child { text-align: left; background-color: #333; }
.grade-table tbody td:first-child { text-align: left; font-weight: 500; }
.grade-table tfoot td { font-weight: bold; background-color: #f0f0f0; }
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
        <li><a class="active" href="grades.php">Grades</a></li>
    </ul>
</div>

<div class="header" id="header">
    <div class="title">Grades</div>
    <div>
        <button id="toggleSidebar">☰</button>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

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