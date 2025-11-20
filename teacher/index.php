<?php
// FIX: Start session for future authentication and check if the teacher is logged in.
// session_start(); 
require_once "teacher_functions.php";
$teacherdb = new TeacherDB();

// !!! CRITICAL FIX REQUIRED: 
// Replace the hardcoded ID with the authenticated session user ID once login is set up.
// For now, retaining the hardcoded ID = 1 for testing purposes.
$teacher_id = 1;

// 1. INITIALIZE ALL VARIABLES to prevent Undefined Variable Warnings (FIX)
$teacher = null;
$advisory = null;
$loads = [];
$teacher_name = "Teacher";
$teacher_fid = "N/A";
$teacher_status = "N/A";

// 2. LOAD DATA
$teacher   = $teacherdb->getTeacherInfo($teacher_id);
$advisory  = $teacherdb->getAdvisorySection($teacher_id);
$loads     = $teacherdb->getTeacherLoads($teacher_id);

// 3. ASSIGN DISPLAY VARIABLES BASED ON LOADED DATA (FIX)
// This ensures variables are defined even if $teacher is null.
if ($teacher) {
    // Check if the keys exist before assigning, aligning with teacher_functions.php output
    $teacher_name   = $teacher['fullname'] ?? "Sample Teacher";
    $teacher_fid    = $teacher['faculty_id'] ?? "N/A";
    $teacher_status = $teacher['status'] ?? "N/A";
}
// Note: $advisory and $loads are checked directly in the HTML below.
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="teacher_styles.css"> 
</head>

<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content" id="mainContent">

    <div class="card">
        <h3>Welcome, <?php echo htmlspecialchars($teacher_name); ?></h3>
        <p>Faculty ID: <?php echo htmlspecialchars($teacher_fid); ?></p>
        <p>Status: <?php echo htmlspecialchars($teacher_status); ?></p>
    </div>

    <div class="card">
        <h3>Your Advisory Section</h3>
        <?php if ($advisory && isset($advisory['section_name'])): ?>
            <p>Section: <strong><?php echo htmlspecialchars($advisory['section_name']); ?></strong></p>
            <p>Grade Level: <?php echo htmlspecialchars($advisory['grade_level']); ?></p>
        <?php else: ?>
            <p>No advisory assigned.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Your Subject Loads</h3>

        <?php if ($loads && count($loads) > 0): ?>
            <table>
                <thead style="background:#007bff; color:white;">
                    <tr>
                        <th>Section</th>
                        <th>Grade</th>
                        <th>Subject</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loads as $load): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($load['section_name']); ?></td>
                        <td><?php echo htmlspecialchars($load['grade_level'] . $load['section_letter']); ?></td>
                        <td><?php echo htmlspecialchars($load['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($load['assignment_type']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No subject loads found.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>