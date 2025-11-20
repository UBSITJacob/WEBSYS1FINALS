<?php
// Ensure authenticated teacher
session_start();
if (!isset($_SESSION['teacher'])) {
    header('Location: ../index.php');
    exit;
}

require_once "teacher_functions.php";
$teacherdb = new TeacherDB();

// Use authenticated teacher id from session
$teacher_id = isset($_SESSION['teacher']['id']) ? (int)$_SESSION['teacher']['id'] : 0;
if ($teacher_id === 0) {
    header('Location: ../index.php');
    exit;
}

// Load teacher loads (sections + subjects the teacher is handling)
$loads = $teacherdb->getTeacherLoads($teacher_id);

// Defaults
$message = "";
$message_status = ""; // "ok" or "err"
$selected_section_id = "";
$selected_academic_year = "";

// Simple helper to suggest current AY (e.g., 2025-2026)
function getDefaultAcademicYear() {
    $year = (int)date('Y');
    $next = $year + 1;
    return $year . "-" . $next;
}

// Handle enrollment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_submit'])) {
    $school_id     = isset($_POST['school_id']) ? trim($_POST['school_id']) : "";
    $section_id    = isset($_POST['section_id']) ? trim($_POST['section_id']) : "";
    $academic_year = isset($_POST['academic_year']) ? trim($_POST['academic_year']) : "";

    if ($school_id !== "" && $section_id !== "" && $academic_year !== "") {

        // You must implement this in TeacherDB (shown below in this answer)
        if (method_exists($teacherdb, 'enrollStudentBySchoolId')) {
            list($ok, $msg) = $teacherdb->enrollStudentBySchoolId($school_id, (int)$section_id, $academic_year);
            $message = $msg;
            $message_status = $ok ? "ok" : "err";

            if ($ok) {
                $selected_section_id = $section_id;
                $selected_academic_year = $academic_year;
            }
        } else {
            $message = "❌ enrollStudentBySchoolId() is not yet defined in TeacherDB. Please add it in teacher_functions.php.";
            $message_status = "err";
        }

    } else {
        $message = "⚠️ Please fill in School ID / LRN, Section, and Academic Year.";
        $message_status = "err";
    }
}

// If the form already selected something, keep those values
if ($selected_section_id === "" && isset($_POST['section_id'])) {
    $selected_section_id = $_POST['section_id'];
}
if ($selected_academic_year === "" && isset($_POST['academic_year'])) {
    $selected_academic_year = $_POST['academic_year'];
}
if ($selected_academic_year === "") {
    $selected_academic_year = getDefaultAcademicYear();
}

// Get students in the currently selected section + AY
$students_in_section = [];
if ($selected_section_id !== "" && $selected_academic_year !== "" && method_exists($teacherdb, 'getSectionStudents')) {
    $students_in_section = $teacherdb->getSectionStudents((int)$selected_section_id, $selected_academic_year);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher | Enrollment</title>
    <link rel="stylesheet" href="teacher_styles.css">
</head>
<body>

<?php include "sidebar.php"; ?>
<?php include "header.php"; ?>

<div class="main-content">

    <div class="card">
        <h2>Student Enrollment</h2>

        <?php if ($message !== ""): ?>
            <div class="message <?php echo htmlspecialchars($message_status); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($loads)): ?>
            <p>No assigned sections found for your account.</p>
        <?php else: ?>
            <form method="post">
                <div class="form-row" style="margin-bottom:10px;">
                    <label for="school_id">School ID / LRN:</label><br>
                    <input type="text" name="school_id" id="school_id" required>
                </div>

                <div class="form-row" style="margin-bottom:10px;">
                    <label for="section_id">Section:</label><br>
                    <select name="section_id" id="section_id" required>
                        <option value="">-- Select Section --</option>
                        <?php foreach ($loads as $load): ?>
                            <?php
                            // Try to be flexible with array keys coming from getTeacherLoads()
                            $sec_id        = isset($load['section_id']) ? $load['section_id'] : (isset($load['id']) ? $load['id'] : "");
                            $grade_level   = isset($load['grade_level']) ? $load['grade_level'] : "";
                            $section_name  = isset($load['section_name']) ? $load['section_name'] : "";
                            $section_letter= isset($load['section_letter']) ? $load['section_letter'] : "";
                            $strand_name   = isset($load['strand_name']) ? $load['strand_name'] : "";
                            $subject_name  = isset($load['subject_name']) ? $load['subject_name'] : "";
                            ?>
                            <option value="<?php echo htmlspecialchars($sec_id); ?>"
                                <?php echo ($selected_section_id == $sec_id) ? "selected" : ""; ?>>
                                <?php
                                    $label = "G" . $grade_level . " - " . $section_name;
                                    if ($section_letter !== "") {
                                        $label .= " (" . $section_letter . ")";
                                    }
                                    if ($strand_name !== "") {
                                        $label .= " - " . $strand_name;
                                    }
                                    if ($subject_name !== "") {
                                        $label .= " | " . $subject_name;
                                    }
                                    echo htmlspecialchars($label);
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row" style="margin-bottom:10px;">
                    <label for="academic_year">Academic Year:</label><br>
                    <input type="text" name="academic_year" id="academic_year"
                           value="<?php echo htmlspecialchars($selected_academic_year); ?>"
                           placeholder="2025-2026" required>
                </div>

                <button type="submit" name="enroll_submit">Enroll Student</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (!empty($students_in_section)): ?>
        <div class="card">
            <h3>Enrolled Students — Section
                <?php
                    // Show selected section name from the loads list if possible
                    $section_label = "";
                    foreach ($loads as $load) {
                        $sec_id = isset($load['section_id']) ? $load['section_id'] : (isset($load['id']) ? $load['id'] : "");
                        if ($sec_id == $selected_section_id) {
                            $grade_level   = isset($load['grade_level']) ? $load['grade_level'] : "";
                            $section_name  = isset($load['section_name']) ? $load['section_name'] : "";
                            $section_letter= isset($load['section_letter']) ? $load['section_letter'] : "";
                            $strand_name   = isset($load['strand_name']) ? $load['strand_name'] : "";
                            $section_label = "G{$grade_level} {$section_name}";
                            if ($section_letter !== "") {
                                $section_label .= " ({$section_letter})";
                            }
                            if ($strand_name !== "") {
                                $section_label .= " - {$strand_name}";
                            }
                            break;
                        }
                    }
                    echo htmlspecialchars($section_label);
                ?>
                — A.Y. <?php echo htmlspecialchars($selected_academic_year); ?>
            </h3>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>School ID / LRN</th>
                        <th>Student Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($students_in_section as $stu): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars(isset($stu['school_id']) ? $stu['school_id'] : ""); ?></td>
                            <td><?php echo htmlspecialchars(isset($stu['fullname']) ? $stu['fullname'] : ""); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($selected_section_id !== "" && $selected_academic_year !== ""): ?>
        <div class="card">
            <h3>No students enrolled yet for the selected section and academic year.</h3>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
