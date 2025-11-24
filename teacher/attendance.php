<?php
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

// load teacher loads (sections + subjects)
$loads = $teacherdb->getTeacherLoads($teacher_id);

$message = "";
$message_status = "";

// --- DEFAULTS / CONTEXT ---
$selected_section_id    = "";
$selected_subject_id    = "";
$selected_year          = date('Y');
$selected_month         = date('n'); // 1–12
$selected_academic_year = "2024-2025"; // Default system AY

// --- 1. HANDLE SAVE ATTENDANCE (POST) ---
if (isset($_POST['save_attendance'])) {
    $section_id = trim($_POST['section_id']);
    $subject_id = trim($_POST['subject_id']);
    $year       = (int)$_POST['attendance_year'];
    $month      = (int)$_POST['attendance_month'];

    $selected_section_id = $section_id;
    $selected_subject_id = $subject_id;
    $selected_year       = $year;
    $selected_month      = $month;

    if ($section_id !== "" && $subject_id !== "" && $year > 0 && $month > 0) {
        if (!empty($_POST['days_present']) && is_array($_POST['days_present'])) {
            $ok_count = 0;
            foreach ($_POST['days_present'] as $student_id => $days_present) {
                $days_present = trim($days_present);
                if ($days_present === "") continue;
                if (!is_numeric($days_present) || (int)$days_present < 0 || (int)$days_present > 31) {
                     $message = "⚠️ Invalid attendance value submitted for a student.";
                     $message_status = "err";
                     continue;
                }
                
                $saved = $teacherdb->saveAttendance(
                    (int)$student_id,
                    (int)$section_id,
                    (int)$subject_id,
                    (int)$teacher_id,
                    $year,
                    $month,
                    (int)$days_present
                );
                if ($saved) {
                    $ok_count++;
                }
            }
            if ($ok_count > 0 && $message_status !== "err") {
                $message = "✅ Saved attendance for {$ok_count} student(s).";
                $message_status = "ok";
            } elseif ($message_status !== "err") {
                $message = "⚠️ No valid attendance values were submitted.";
                $message_status = "err";
            }
        } else {
            $message = "⚠️ No attendance values submitted.";
            $message_status = "err";
        }
    } else {
        $message = "⚠️ Please choose section, subject, year, and month.";
        $message_status = "err";
    }
}

// --- 2. HANDLE LOAD (GET) ---
if (isset($_GET['section_id'])) {
    $selected_section_id = $_GET['section_id'];
}
if (isset($_GET['subject_id'])) {
    $selected_subject_id = $_GET['subject_id'];
}
if (isset($_GET['attendance_year'])) {
    $selected_year = (int)$_GET['attendance_year'];
}
if (isset($_GET['attendance_month'])) {
    $selected_month = (int)$_GET['attendance_month'];
}

// --- 3. DATA LOADING ---
$students_in_section = [];
$existing_attendance = [];
$class_info = []; // To store the display names

if ($selected_section_id !== "") {
    // We assume students are enrolled in the default Academic Year
    $students_in_section = $teacherdb->getSectionStudents((int)$selected_section_id, $selected_academic_year);
    
    // Determine the section and subject names for display
    foreach ($loads as $load) {
        if ($load['section_id'] == $selected_section_id) {
            // Get section info from any load linked to this section
            $class_info['section_label'] = $load['section_name'] . " (G" . $load['grade_level'] . $load['section_letter'] . ")";
        }
        if ($load['subject_id'] == $selected_subject_id && $load['section_id'] == $selected_section_id) {
            // Get subject name from the exact load match
            $class_info['subject_label'] = $load['subject_name'];
        }
    }
}

// load existing attendance to prefill
if ($selected_section_id !== "" && $selected_subject_id !== "" && $selected_year && $selected_month) {
    $existing_attendance = $teacherdb->getExistingAttendance(
        (int)$selected_section_id,
        (int)$selected_subject_id,
        (int)$selected_year,
        (int)$selected_month
    );
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
    <link rel="stylesheet" href="teacher_styles.css"> 
    </head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">

    <div class="card">
        <h2>Attendance</h2>
        <p>Select a section, subject, year and month to encode or edit monthly attendance.</p>

        <?php if (isset($message_status) && $message !== ""): ?>
            <div class="message <?php echo $message_status; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="get" action="attendance.php">
            <div class="form-row">
                <label for="section_id">Section:</label>
                <select name="section_id" id="section_id" required>
                    <option value="">-- Select Section --</option>
                    <?php if ($loads): ?>
                        <?php
                        $shown_sections = [];
                        foreach ($loads as $load):
                            if (in_array($load['section_id'], $shown_sections)) continue;
                            $shown_sections[] = $load['section_id'];
                        ?>
                            <option value="<?php echo htmlspecialchars($load['section_id']); ?>"
                                <?php if ($selected_section_id == $load['section_id']) echo 'selected'; ?>>
                                <?php
                                    echo htmlspecialchars(
                                        $load['section_name'] .
                                        " (G" . $load['grade_level'] . $load['section_letter'] . ")"
                                    );
                                ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-row">
                <label for="subject_id">Subject:</label>
                <select name="subject_id" id="subject_id" required>
                    <option value="">-- Select Subject --</option>
                    <?php if ($loads): ?>
                        <?php 
                        $shown_subjects = [];
                        foreach ($loads as $load):
                            // Filter subjects if a section is selected, or list all subjects if none is selected
                            if ($selected_section_id !== "" && $load['section_id'] != $selected_section_id) continue;
                            
                            // Prevent subject duplication in the list
                            if (in_array($load['subject_id'], $shown_subjects)) continue;
                            $shown_subjects[] = $load['subject_id'];
                        ?>
                            <option value="<?php echo htmlspecialchars($load['subject_id']); ?>"
                                <?php if ($selected_subject_id == $load['subject_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($load['subject_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-row">
                <label for="attendance_year">Year:</label>
                <input type="number" name="attendance_year" id="attendance_year"
                       value="<?php echo htmlspecialchars($selected_year); ?>" required>
            </div>

            <div class="form-row">
                <label for="attendance_month">Month (1-12):</label>
                <input type="number" name="attendance_month" id="attendance_month"
                       min="1" max="12"
                       value="<?php echo htmlspecialchars($selected_month); ?>" required>
            </div>

            <button type="submit">Load Students</button>
        </form>
    </div>

    <?php if ($selected_section_id !== "" && $selected_subject_id !== "" && !empty($students_in_section)): ?>
        <div class="card">
            <h3>Enter / Edit Attendance</h3>
            <p>
                Section:
                <strong>
                    <?php echo htmlspecialchars($class_info['section_label'] ?? 'N/A'); ?>
                </strong><br>
                Subject:
                <strong>
                    <?php echo htmlspecialchars($class_info['subject_label'] ?? 'N/A'); ?>
                </strong><br>
                Month: <strong><?php echo htmlspecialchars(date('F', mktime(0, 0, 0, $selected_month, 1)) . ' ' . $selected_year); ?></strong>
            </p>

            <form method="post" action="attendance.php">
                <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($selected_section_id); ?>">
                <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($selected_subject_id); ?>">
                <input type="hidden" name="attendance_year" value="<?php echo htmlspecialchars($selected_year); ?>">
                <input type="hidden" name="attendance_month" value="<?php echo htmlspecialchars($selected_month); ?>">

                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>School ID</th>
                            <th>Full Name</th>
                            <th>Days Present (0-31)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students_in_section as $stu): ?>
                            <?php
                            $sid = $stu['student_id'];
                            $prefill = isset($existing_attendance[$sid]) ? $existing_attendance[$sid] : "";
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sid); ?></td>
                                <td><?php echo htmlspecialchars($stu['school_id']); ?></td>
                                <td><?php echo htmlspecialchars($stu['fullname']); ?></td>
                                <td>
                                    <input type="number"
                                           name="days_present[<?php echo $sid; ?>]"
                                           min="0" max="31"
                                           value="<?php echo htmlspecialchars($prefill); ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <br>
                <button type="submit" name="save_attendance">Save Attendance</button>
            </form>
        </div>
    <?php elseif ($selected_section_id !== "" && $selected_subject_id !== ""): ?>
        <div class="card">
            <h3>Enter Attendance</h3>
            <p>No students found for this section (AY <?php echo htmlspecialchars($selected_academic_year); ?>). Please check enrollment data.</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>