<?php
require_once "teacher_functions.php";
$teacherdb = new TeacherDB();

// TEMP — hardcoded teacher ID (replace with session later)
$teacher_id = 1;

// load teacher loads (sections + subjects)
$loads = $teacherdb->getTeacherLoads($teacher_id);

$message = "";

// defaults
$selected_section_id       = "";
$selected_subject_id       = "";
$selected_academic_year    = "2024-2025";
$selected_grading_period   = "1st Qtr";

// handle save grades
if (isset($_POST['save_grades'])) {
    $section_id      = trim($_POST['section_id']);
    $subject_id      = trim($_POST['subject_id']);
    $academic_year   = trim($_POST['academic_year']);
    $grading_period  = trim($_POST['grading_period']);

    $selected_section_id      = $section_id;
    $selected_subject_id      = $subject_id;
    $selected_academic_year   = $academic_year;
    $selected_grading_period  = $grading_period;

    if ($section_id !== "" && $subject_id !== "" && $grading_period !== "") {
        if (!empty($_POST['grade']) && is_array($_POST['grade'])) {
            $ok_count = 0;
            foreach ($_POST['grade'] as $student_id => $grade_value) {
                $grade_value = trim($grade_value);
                if ($grade_value === "") {
                    continue; // skip empty inputs
                }
                if (!is_numeric($grade_value)) {
                    continue; // skip invalid values
                }
                $saved = $teacherdb->saveGrade(
                    (int)$student_id,
                    (int)$section_id,
                    (int)$subject_id,
                    (int)$teacher_id,
                    $grading_period,
                    (float)$grade_value
                );
                if ($saved) {
                    $ok_count++;
                }
            }
            if ($ok_count > 0) {
                $message = "✅ Saved {$ok_count} grade(s) successfully.";
            } else {
                $message = "⚠️ No valid grades were submitted.";
            }
        } else {
            $message = "⚠️ No grades submitted.";
        }
    } else {
        $message = "⚠️ Please choose section, subject, and grading period.";
    }
}

// handle loading of students from GET (when user clicks "Load Students")
if (isset($_GET['section_id'])) {
    $selected_section_id = $_GET['section_id'];
}
if (isset($_GET['subject_id'])) {
    $selected_subject_id = $_GET['subject_id'];
}
if (isset($_GET['academic_year'])) {
    $selected_academic_year = $_GET['academic_year'];
}
if (isset($_GET['grading_period'])) {
    $selected_grading_period = $_GET['grading_period'];
}

// load students for the selected section/year
$students_in_section = [];
$existing_grades     = [];

if ($selected_section_id !== "" && $selected_academic_year !== "") {
    $students_in_section = $teacherdb->getSectionStudents((int)$selected_section_id, $selected_academic_year);
}

// load existing grades for prefill
if ($selected_section_id !== "" && $selected_subject_id !== "" && $selected_grading_period !== "") {
    $existing_grades = $teacherdb->getExistingGrades(
        (int)$selected_section_id,
        (int)$selected_subject_id,
        $selected_grading_period
    );
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Grades</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial;
            overflow-x: hidden;
        }

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

        .sidebar {
            position: fixed;
            top: 60px;
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

        .content {
            margin-left: 240px;
            margin-top: 80px;
            padding: 20px;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }

        .card {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            box-shadow: 0px 0px 4px #ccc;
        }

        .form-row {
            margin-bottom: 10px;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 4px;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 7px;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            padding: 8px 14px;
            background: #333;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #555;
        }

        .message {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
            font-size: 14px;
        }

        .message.ok {
            background: #d4edda;
            color: #155724;
        }

        .message.err {
            background: #f8d7da;
            color: #721c24;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 14px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 6px 8px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content">

    <div class="card">
        <h2>Manage Grades</h2>
        <p>Select a section, subject, academic year, and grading period to encode or edit grades.</p>

        <?php if ($message !== ""): ?>
            <div class="message <?php echo (strpos($message, "✅") === 0) ? "ok" : "err"; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- FILTER FORM (GET) -->
        <form method="get" action="grades.php">
            <div class="form-row">
                <label for="section_id">Section:</label>
                <select name="section_id" id="section_id" required>
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
                <label for="academic_year">Academic Year (e.g. 2024-2025):</label>
                <input type="text" name="academic_year" id="academic_year"
                       value="<?php echo htmlspecialchars($selected_academic_year); ?>" required>
            </div>

            <div class="form-row">
                <label for="grading_period">Grading Period:</label>
                <select name="grading_period" id="grading_period" required>
                    <option value="1st Qtr" <?php if ($selected_grading_period == "1st Qtr") echo 'selected'; ?>>1st Qtr</option>
                    <option value="2nd Qtr" <?php if ($selected_grading_period == "2nd Qtr") echo 'selected'; ?>>2nd Qtr</option>
                    <option value="3rd Qtr" <?php if ($selected_grading_period == "3rd Qtr") echo 'selected'; ?>>3rd Qtr</option>
                    <option value="4th Qtr" <?php if ($selected_grading_period == "4th Qtr") echo 'selected'; ?>>4th Qtr</option>
                    <option value="Final"   <?php if ($selected_grading_period == "Final")   echo 'selected'; ?>>Final</option>
                </select>
            </div>

            <button type="submit">Load Students</button>
        </form>
    </div>

    <?php if ($selected_section_id !== "" && $selected_subject_id !== "" && !empty($students_in_section)): ?>
        <div class="card">
            <h3>Enter / Edit Grades</h3>
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
                Academic Year: <strong><?php echo htmlspecialchars($selected_academic_year); ?></strong><br>
                Grading Period: <strong><?php echo htmlspecialchars($selected_grading_period); ?></strong>
            </p>

            <form method="post" action="grades.php">
                <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($selected_section_id); ?>">
                <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($selected_subject_id); ?>">
                <input type="hidden" name="academic_year" value="<?php echo htmlspecialchars($selected_academic_year); ?>">
                <input type="hidden" name="grading_period" value="<?php echo htmlspecialchars($selected_grading_period); ?>">

                <table>
                    <tr>
                        <th>Student ID</th>
                        <th>School ID</th>
                        <th>Full Name</th>
                        <th>Grade</th>
                    </tr>
                    <?php foreach ($students_in_section as $stu): ?>
                        <?php
                        $sid = $stu['student_id'];
                        $prefill = isset($existing_grades[$sid]) ? $existing_grades[$sid] : "";
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sid); ?></td>
                            <td><?php echo htmlspecialchars($stu['school_id']); ?></td>
                            <td><?php echo htmlspecialchars($stu['fullname']); ?></td>
                            <td>
                                <input type="number"
                                       name="grade[<?php echo $sid; ?>]"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       value="<?php echo htmlspecialchars($prefill); ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <br>
                <button type="submit" name="save_grades">Save Grades</button>
            </form>
        </div>
    <?php elseif ($selected_section_id !== "" && $selected_subject_id !== ""): ?>
        <div class="card">
            <h3>Enter Grades</h3>
            <p>No students found for this section and academic year.</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
