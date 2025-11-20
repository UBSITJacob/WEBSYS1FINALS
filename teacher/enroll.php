<?php
// FIX: You MUST start the session to get the authenticated user's ID
// session_start(); 
require_once "teacher_functions.php";
$teacherdb = new TeacherDB();

// !!! CRITICAL FIX REQUIRED: 
// Replace the hardcoded ID with the authenticated session user ID once login is set up.
// Example: $teacher_id = $_SESSION['user_id'] ?? 0;
// Using 1 for now, but this is a security risk until fixed by proper login.
$teacher_id = 1;

// Load teacher info (optional, for display if you want)
$teacher = $teacherdb->getTeacherInfo($teacher_id);
// Load all sections/subjects assigned to this teacher
$loads = $teacherdb->getTeacherLoads($teacher_id);

// Message for feedback
$message = "";
$message_status = ""; // New variable to hold 'ok' or 'err'

// Default values
$selected_section_id = "";
$selected_academic_year = "2024-2025"; // you can change this default

// Handle form submit
if (isset($_POST['enroll_submit'])) {
    $student_id = trim($_POST['student_id']);
    $section_id = trim($_POST['section_id']);
    $academic_year = trim($_POST['academic_year']);

    if ($student_id !== "" && $section_id !== "" && $academic_year !== "") {
        // Save enrollment
        $ok = $teacherdb->enrollStudent((int)$student_id, (int)$section_id, $academic_year);
        if ($ok) {
            $message = "✅ Student ID {$student_id} enrolled/updated successfully.";
            $message_status = "ok";
            // keep the selected values for reloading students
            $selected_section_id = $section_id;
            $selected_academic_year = $academic_year;
        } else {
            $message = "❌ Error while enrolling student. Please check inputs.";
            $message_status = "err";
        }
    } else {
        $message = "⚠️ Please fill in all fields.";
        $message_status = "err";
    }
}

// If section/AY selected (via POST or GET), load students in that section
if (isset($_GET['section_id'])) {
    $selected_section_id = $_GET['section_id'];
}
if (isset($_GET['academic_year'])) {
    $selected_academic_year = $_GET['academic_year'];
}

$students_in_section = [];
if ($selected_section_id !== "" && $selected_academic_year !== "") {
    $students_in_section = $teacherdb->getSectionStudents((int)$selected_section_id, $selected_academic_year);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Enroll Students</title>
    <link rel="stylesheet" href="teacher_styles.css"> 
    </head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">

    <div class="card">
        <h2>Enroll Students</h2>
        <p>Use this form to enroll a student into one of your sections for a specific academic year.</p>

        <?php if (isset($message_status) && $message !== ""): ?>
            <div class="message <?php echo $message_status; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="enroll.php">
            <div class="form-row">
                <label for="student_id">Student ID (user_id):</label>
                <input type="number" name="student_id" id="student_id" required>
            </div>

            <div class="form-row">
                <label for="section_id">Section:</label>
                <select name="section_id" id="section_id" required>
                    <option value="">-- Select Section --</option>
                    <?php if ($loads): ?>
                        <?php
                        // We'll show each section once (even if multiple subjects)
                        $shown_sections = [];
                        foreach ($loads as $load):
                            if (in_array($load['section_id'], $shown_sections)) {
                                continue;
                            }
                            $shown_sections[] = $load['section_id'];
                        ?>
                            <option value="<?php echo $load['section_id']; ?>"
                                <?php if ($selected_section_id == $load['section_id']) echo 'selected'; ?>>
                                <?php
                                    echo htmlspecialchars(
                                        $load['section_name'] .
                                        " (Grade " . $load['grade_level'] . $load['section_letter'] . ")"
                                    );
                                ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-row">
                <label for="academic_year">Academic Year (e.g. 2024-2025):</label>
                <input type="text" name="academic_year" id="academic_year"
                       value="<?php echo htmlspecialchars($selected_academic_year); ?>" required>
            </div>

            <button type="submit" name="enroll_submit">Enroll Student</button>
        </form>
    </div>

    <div class="card">
        <h3>Students in Section</h3>
        <p>Select section and academic year to view currently enrolled students.</p>

        <form method="get" action="enroll.php">
            <div class="form-row">
                <label for="view_section_id">Section:</label>
                <select name="section_id" id="view_section_id" required>
                    <option value="">-- Select Section --</option>
                    <?php if ($loads): ?>
                        <?php
                        $shown_sections = [];
                        foreach ($loads as $load):
                            if (in_array($load['section_id'], $shown_sections)) {
                                continue;
                            }
                            $shown_sections[] = $load['section_id'];
                        ?>
                            <option value="<?php echo $load['section_id']; ?>"
                                <?php if ($selected_section_id == $load['section_id']) echo 'selected'; ?>>
                                <?php
                                    echo htmlspecialchars(
                                        $load['section_name'] .
                                        " (Grade " . $load['grade_level'] . $load['section_letter'] . ")"
                                    );
                                ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-row">
                <label for="view_academic_year">Academic Year:</label>
                <input type="text" name="academic_year" id="view_academic_year"
                       value="<?php echo htmlspecialchars($selected_academic_year); ?>" required>
            </div>

            <button type="submit">View Students</button>
        </form>

        <?php if ($selected_section_id !== "" && $selected_academic_year !== ""): ?>
            <h4>Students for AY <?php echo htmlspecialchars($selected_academic_year); ?></h4>

            <?php if (!empty($students_in_section)): ?>
                <table>
                    <thead style="background:#007bff; color:white;">
                        <tr>
                            <th>Student ID</th>
                            <th>School ID</th>
                            <th>Full Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students_in_section as $stu): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stu['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($stu['school_id']); ?></td>
                                <td><?php echo htmlspecialchars($stu['fullname']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No students enrolled yet for this section and academic year.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>