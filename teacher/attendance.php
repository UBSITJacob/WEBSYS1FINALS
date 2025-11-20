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

// load teacher loads (sections + subjects)
$loads = $teacherdb->getTeacherLoads($teacher_id);

$message = "";
$message_status = ""; // New variable to hold 'ok' or 'err'

// defaults
$selected_section_id    = "";
$selected_subject_id    = "";
$selected_year          = date('Y');
$selected_month         = date('n'); // 1–12
$selected_academic_year = "2024-2025"; // optional if you want to display

// handle save attendance
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
                if ($days_present === "") {
                    continue;
                }
                if (!is_numeric($days_present)) {
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
            if ($ok_count > 0) {
                $message = "✅ Saved attendance for {$ok_count} student(s).";
                $message_status = "ok";
            } else {
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

// handle loading of students + existing attendance
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

// load students for the selected section/year (reuse academic year if needed)
$students_in_section = [];
$existing_attendance = [];

if ($selected_section_id !== "") {
    // we can still use academic year filter if you want; for now using default AY
    $students_in_section = $teacherdb->getSectionStudents((int)$selected_section_id, $selected_academic_year);
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
                <label for="subject_id">Subject:</label>
                <select name="subject_id" id="subject_id" required>
                    <option value="">-- Select Subject --</option>
                    <?php if ($loads): ?>
                        <?php foreach ($loads as $load): ?>
                            <?php if ($selected_section_id !== "" && $load['section_id'] != $selected_section_id) continue; ?>
                            <option value="<?php echo $load['subject_id']; ?>"
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
                    <?php
                    $section_label = "";
                    if ($loads) {
                        foreach ($loads as $load) {
                            if ($load['section_id'] == $selected_section_id) {
                                $section_label = $load['section_name'] . " (Grade " .
                                                 $load['grade_level'] . $load['section_letter'] . ")";
                                break;
                            }
                        }
                    }
                    echo htmlspecialchars($section_label);
                    ?>
                </strong><br>
                Subject:
                <strong>
                    <?php
                    $subject_label = "";
                    if ($loads) {
                        foreach ($loads as $load) {
                            if ($load['subject_id'] == $selected_subject_id) {
                                $subject_label = $load['subject_name'];
                                break;
                            }
                        }
                    }
                    echo htmlspecialchars($subject_label);
                    ?>
                </strong><br>
                Month: <strong><?php echo htmlspecialchars($selected_year . '-' . $selected_month); ?></strong>
            </p>

            <form method="post" action="attendance.php">
                <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($selected_section_id); ?>">
                <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($selected_subject_id); ?>">
                <input type="hidden" name="attendance_year" value="<?php echo htmlspecialchars($selected_year); ?>">
                <input type="hidden" name="attendance_month" value="<?php echo htmlspecialchars($selected_month); ?>">

                <table>
                    <thead style="background:#007bff; color:white;">
                        <tr>
                            <th>Student ID</th>
                            <th>School ID</th>
                            <th>Full Name</th>
                            <th>Days Present</th>
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
            <p>No students found for this section.</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>