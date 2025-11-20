<?php
require_once "teacher_functions.php";
$teacherdb = new TeacherDB();

// TEMP — remove when login works
$teacher_id = 1;

// LOAD DATA
$teacher   = $teacherdb->getTeacherInfo($teacher_id);
$advisory  = $teacherdb->getAdvisorySection($teacher_id);
$loads     = $teacherdb->getTeacherLoads($teacher_id);

// SAFETY: prevent warnings if no data is found
$teacher_name   = ($teacher && isset($teacher['fullname']))    ? $teacher['fullname']    : "Sample Teacher";
$teacher_fid    = ($teacher && isset($teacher['faculty_id']))  ? $teacher['faculty_id']  : "N/A";
$teacher_status = ($teacher && isset($teacher['status']))      ? $teacher['status']      : "N/A";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
<style>

body {
    margin: 0;
    padding: 0;
    font-family: Arial;
    overflow-x: hidden; /* stops horizontal scroll */
}

/* TOP BAR */
.topbar {
    width: 100%;
    background: #333;
    color: white;
    padding: 15px;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 10;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    top: 60px; /* pushes below header */
    left: 0;
    width: 220px;
    height: calc(100vh - 60px);
    background: #2d2d2d;
    padding-top: 20px;
    overflow-y: auto;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    padding: 12px 20px;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: block;
}

.sidebar ul li:hover {
    background: #444;
}

/* CONTENT AREA */
.content {
    margin-left: 240px; /* space for sidebar */
    margin-top: 80px;   /* space for header */
    padding: 20px;
    width: calc(100% - 260px); 
    box-sizing: border-box;
}

/* CARDS */
.card {
    background: white;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
    box-shadow: 0px 0px 4px #ccc;
}
</style>
</head>

<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content">

    <!-- TEACHER INFO CARD -->
    <div class="card">
        <h3>Welcome,<?php echo htmlspecialchars($teacher_name); ?></h3>
        <p>Faculty ID:<?php echo htmlspecialchars($teacher_fid); ?></p>
        <p>Status: <?php echo htmlspecialchars($teacher_status); ?></p>
    </div>

    <!-- ADVISORY CLASS CARD -->
    <div class="card">
        <h3>Your Advisory Section</h3>
        <?php if ($advisory && isset($advisory['section_name'])): ?>
            <p>Section: <strong><?php echo htmlspecialchars($advisory['section_name']); ?></strong></p>
            <p>Grade Level: <?php echo htmlspecialchars($advisory['grade_level']); ?></p>
        <?php else: ?>
            <p>No advisory assigned.</p>
        <?php endif; ?>
    </div>

    <!-- SUBJECT LOADS CARD -->
    <div class="card">
        <h3>Your Subject Loads</h3>

        <?php if ($loads && count($loads) > 0): ?>
            <table border="1" cellpadding="6" width="100%">
                <tr>
                    <th>Section</th>
                    <th>Grade</th>
                    <th>Subject</th>
                    <th>Type</th>
                </tr>
                <?php foreach ($loads as $load): ?>
                <tr>
                    <td><?php echo htmlspecialchars($load['section_name']); ?></td>
                    <td><?php echo htmlspecialchars($load['grade_level'] . $load['section_letter']); ?></td>
                    <td><?php echo htmlspecialchars($load['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($load['assignment_type']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No subject loads found.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
