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

// Handle enrollment submission (Unchanged logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_submit'])) {
    $school_id     = isset($_POST['school_id']) ? trim($_POST['school_id']) : "";
    $section_id    = isset($_POST['section_id']) ? trim($_POST['section_id']) : "";
    $academic_year = isset($_POST['academic_year']) ? trim($_POST['academic_year']) : "";

    if ($school_id !== "" && $section_id !== "" && $academic_year !== "") {
        if (method_exists($teacherdb, 'enrollStudentBySchoolId')) {
            list($ok, $msg) = $teacherdb->enrollStudentBySchoolId($school_id, (int)$section_id, $academic_year);
            $message = $msg;
            $message_status = $ok ? "ok" : "err";

            if ($ok) {
                // Clear School ID field on success
                $_POST['school_id'] = ''; 
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

// --- UX FIX 1: Generate a unique list of sections handled by the teacher ---
$unique_sections = [];
foreach ($loads as $load) {
    $sec_id = $load['section_id'];
    if (!isset($unique_sections[$sec_id])) {
        $unique_sections[$sec_id] = [
            'id' => $sec_id,
            'label' => "G{$load['grade_level']} - {$load['section_name']} ({$load['section_letter']})",
            'grade_level' => $load['grade_level']
        ];
    }
}
// Sort by Grade Level for cleaner display
uasort($unique_sections, fn($a, $b) => $a['grade_level'] <=> $b['grade_level']);


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
    <style>
        /* Styles for better form usability */
        .message.ok { border-left: 5px solid #28a745; background-color: #d4edda; color: #155724; }
        .message.err { border-left: 5px solid #dc3545; background-color: #f8d7da; color: #721c24; }
        .form-row input[type="text"], .form-row select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.95em; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    </style>
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
            <p>No assigned sections found for your account. You must be assigned sections to enroll students.</p>
        <?php else: ?>
            <form method="post">
                <div class="form-row" style="margin-bottom:10px;">
                    <label for="school_id">School ID / LRN:</label><br>
                    <input type="text" name="school_id" id="school_id" required 
                           value="<?php echo htmlspecialchars($_POST['school_id'] ?? ''); ?>">
                </div>

                <div class="form-row" style="margin-bottom:10px;">
                    <label for="section_id">Section:</label><br>
                    <select name="section_id" id="section_id" required>
                        <option value="">-- Select Section --</option>
                        <?php 
                        // UX FIX: Use the unique list of sections generated above
                        foreach ($unique_sections as $sec): 
                        ?>
                            <option value="<?php echo htmlspecialchars($sec['id']); ?>"
                                <?php echo ($selected_section_id == $sec['id']) ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($sec['label']); ?>
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
                    // Display the selected section label clearly
                    $section_label = $unique_sections[$selected_section_id]['label'] ?? 'N/A';
                    echo htmlspecialchars($section_label);
                ?>
                — A.Y. <?php echo htmlspecialchars($selected_academic_year); ?>
            </h3>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>School ID</th>
                        <th>Full Name</th>
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