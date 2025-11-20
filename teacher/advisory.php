<?php
// FIX: You MUST start the session to get the authenticated user's ID
// session_start(); 
require_once "teacher_functions.php";
$teacherdb = new TeacherDB();

// !!! CRITICAL FIX REQUIRED: 
// Replace the hardcoded ID with the authenticated session user ID.
// Example: $teacher_id = $_SESSION['user_id'] ?? 0;
// Using 1 for now, but this is a security risk until fixed by proper login.
$teacher_id = 1;

// get advisory section (if any)
$advisory = $teacherdb->getAdvisorySection($teacher_id);

// default academic year
$selected_academic_year = "2024-2025";

// allow changing academic year via GET
if (isset($_GET['academic_year'])) {
    $selected_academic_year = trim($_GET['academic_year']);
}

// load students if advisory exists
$advisory_students = [];
if ($advisory && isset($advisory['id'])) {
    $advisory_students = $teacherdb->getSectionStudents(
        (int)$advisory['id'],
        $selected_academic_year
    );
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Advisory Class</title>
    <link rel="stylesheet" href="teacher_styles.css">
    </head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">

    <div class="card">
        <h2>Advisory Class</h2>

        <?php if ($advisory && isset($advisory['section_name'])): ?>
            <p>
                Section:
                <strong><?php echo htmlspecialchars($advisory['section_name']); ?></strong><br>
                Grade Level:
                <strong><?php echo htmlspecialchars($advisory['grade_level']); ?></strong><br>
                <?php if (isset($advisory['section_letter'])): ?>
                    Section Letter:
                    <strong><?php echo htmlspecialchars($advisory['section_letter']); ?></strong><br>
                <?php endif; ?>
            </p>

            <form method="get" action="advisory.php">
                <div class="form-row">
                    <label for="academic_year">Academic Year (e.g. 2024-2025):</label>
                    <input type="text" name="academic_year" id="academic_year"
                           value="<?php echo htmlspecialchars($selected_academic_year); ?>" required>
                </div>
                <button type="submit">Load Students</button>
            </form>

        <?php else: ?>
            <p>You are not assigned as an adviser to any section.</p>
        <?php endif; ?>
    </div>

    <?php if ($advisory && isset($advisory['section_name'])): ?>
        <div class="card">
            <h3>Students in Advisory (AY <?php echo htmlspecialchars($selected_academic_year); ?>)</h3>

            <?php if (!empty($advisory_students)): ?>
                <table>
                    <thead style="background:#007bff; color:white;">
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>School ID</th>
                            <th>Full Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        foreach ($advisory_students as $stu):
                        ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($stu['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($stu['school_id']); ?></td>
                                <td><?php echo htmlspecialchars($stu['fullname']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="small-text">
                    These students are enrolled in your advisory section for the selected academic year.
                </p>
            <?php else: ?>
                <p>No students enrolled in this advisory section for AY <?php echo htmlspecialchars($selected_academic_year); ?>.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>