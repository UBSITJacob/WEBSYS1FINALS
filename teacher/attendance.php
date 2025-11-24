<?php
session_start();
if (!isset($_SESSION['teacher'])) {
    header('Location: ../index.php');
    exit;
}

require_once "teacher_functions.php";
$teacherdb = new TeacherDB();

$teacher_id = isset($_SESSION['teacher']['id']) ? (int)$_SESSION['teacher']['id'] : 0;
if ($teacher_id === 0) {
    header('Location: ../index.php');
    exit;
}

$loads = $teacherdb->getTeacherLoads($teacher_id);

$message = "";
$message_status = "";

$selected_section_id    = "";
$selected_subject_id    = "";
$selected_year          = date('Y');
$selected_month         = date('n');
$selected_academic_year = "2024-2025";

// ---------------------------------------------
// SAVE DAILY ATTENDANCE
// ---------------------------------------------
if (isset($_POST['save_attendance'])) {

    $section_id = (int)$_POST['section_id'];
    $subject_id = (int)$_POST['subject_id'];
    $year       = (int)$_POST['attendance_year'];
    $month      = (int)$_POST['attendance_month'];

    $selected_section_id = $section_id;
    $selected_subject_id = $subject_id;
    $selected_year       = $year;
    $selected_month      = $month;

    if ($section_id && $subject_id && $year && $month) {

        if (!empty($_POST['att']) && is_array($_POST['att'])) {

            $ok = 0;

            foreach ($_POST['att'] as $student_id => $days) {
                foreach ($days as $day => $status) {

                    if ($status === "") continue;

                    $date = sprintf("%04d-%02d-%02d", $year, $month, $day);

                    $teacherdb->saveDailyAttendance(
                        (int)$student_id,
                        $section_id,
                        $subject_id,
                        $teacher_id,
                        $date,
                        $status
                    );
                }
            }

            // COMPUTE MONTHLY TOTALS (Present only = blank or P)
            $students = $teacherdb->getSectionStudents($section_id, $selected_academic_year);

            foreach ($students as $stu) {
                $sid = $stu['student_id'];

                $daily = $teacherdb->getDailyAttendance($section_id, $subject_id, $year, $month);

                $presentCount = 0;
                foreach ($daily as $row) {
                    if ($row['student_id'] == $sid) {
                        if ($row['status'] == 'P') $presentCount++;
                    }
                }

                $teacherdb->saveMonthlySummary(
                    $sid,
                    $section_id,
                    $subject_id,
                    $teacher_id,
                    $year,
                    $month,
                    $presentCount
                );
            }

            $message = "✅ Daily attendance saved successfully.";
            $message_status = "ok";

        } else {
            $message = "⚠️ No attendance submitted.";
            $message_status = "err";
        }

    } else {
        $message = "⚠️ Please select section, subject, year, and month.";
        $message_status = "err";
    }
}

// ---------------------------------------------
// LOAD FILTER VALUES
// ---------------------------------------------
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

$students_in_section = [];
$class_info = [];

if ($selected_section_id !== "") {
    $students_in_section = $teacherdb->getSectionStudents((int)$selected_section_id, $selected_academic_year);

    foreach ($loads as $load) {
        if ($load['section_id'] == $selected_section_id) {
            $class_info['section_label'] = $load['section_name'] . 
                " (G" . $load['grade_level'] . $load['section_letter'] . ")";
        }
        if ($load['section_id'] == $selected_section_id && $load['subject_id'] == $selected_subject_id) {
            $class_info['subject_label'] = $load['subject_name'];
        }
    }
}

// Load daily attendance
$daily_att = [];
if ($selected_section_id && $selected_subject_id) {
    $raw = $teacherdb->getDailyAttendance(
        (int)$selected_section_id,
        (int)$selected_subject_id,
        (int)$selected_year,
        (int)$selected_month
    );

    foreach ($raw as $r) {
        $stu = $r['student_id'];
        $day = (int)date("d", strtotime($r['attendance_date']));
        $daily_att[$stu][$day] = $r['status'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SF2 Daily Attendance</title>
    <link rel="stylesheet" href="teacher_styles.css">
    <style>
        table.sf2 td, table.sf2 th {
            padding: 3px;
            font-size: 12px;
            text-align: center;
        }
        .att-cell select {
            width: 50px;
        }
       
    .sf2-wrapper {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 10px;
        border: 1px solid #ccc;
    }

    table.sf2 {
        border-collapse: collapse;
        min-width: 2200px; /* FORCE wide layout */
    }

    table.sf2 th,
    table.sf2 td {
        border: 1px solid #999;
        padding: 3px;
        font-size: 12px;
        text-align: center;
        min-width: 45px; /* Make each day column wider */
    }

    /* Freeze the first 2 columns */
    table.sf2 th:nth-child(1),
    table.sf2 td:nth-child(1) {
        position: sticky;
        left: 0;
        z-index: 20;
        background: white;
    }

    table.sf2 th:nth-child(2),
    table.sf2 td:nth-child(2) {
        position: sticky;
        left: 60px;
        z-index: 20;
        background: white;
    }

    /* Freeze P / A / T columns on the right */
    table.sf2 th.sf2-total,
    table.sf2 td.sf2-total {
        position: sticky;
        right: 0;
        z-index: 20;
        background: #f1f1f1;
    }

    .att-cell select {
        width: 40px !important;
        font-size: 11px;
        padding: 0;
        margin: 0;
    }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">

<div class="card">
    <h2>SF2 Daily Attendance</h2>

    <?php if ($message): ?>
        <div class="message <?php echo $message_status; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <form method="get" action="attendance.php">
        <div class="form-row">
            <label>Section:</label>
            <select name="section_id" required>
                <option value="">-- Select Section --</option>
                <?php
                $seen = [];
                foreach ($loads as $load):
                    if (in_array($load['section_id'], $seen)) continue;
                    $seen[] = $load['section_id'];
                ?>
                    <option value="<?php echo $load['section_id']; ?>"
                        <?php if ($selected_section_id == $load['section_id']) echo "selected"; ?>>
                        <?php
                            echo $load['section_name'] . 
                                 " (G" . $load['grade_level'] . $load['section_letter'] . ")";
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Subject:</label>
            <select name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($loads as $load):
                    if ($selected_section_id && $load['section_id'] != $selected_section_id) continue;
                ?>
                    <option value="<?php echo $load['subject_id']; ?>"
                        <?php if ($selected_subject_id == $load['subject_id']) echo "selected"; ?>>
                        <?php echo $load['subject_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label>Year:</label>
            <input type="number" name="attendance_year" value="<?php echo $selected_year; ?>">
        </div>

        <div class="form-row">
            <label>Month:</label>
            <input type="number" name="attendance_month" min="1" max="12"
                   value="<?php echo $selected_month; ?>">
        </div>

        <button type="submit">Load SF2</button>
    </form>
</div>

<?php if (!empty($students_in_section)): ?>
<div class="card">
    <h3>Daily Attendance — SF2 Format</h3>

    <form method="post" action="attendance.php">

        <input type="hidden" name="section_id" value="<?php echo $selected_section_id; ?>">
        <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">
        <input type="hidden" name="attendance_year" value="<?php echo $selected_year; ?>">
        <input type="hidden" name="attendance_month" value="<?php echo $selected_month; ?>">

        <div class="sf2-wrapper">
        <table class="sf2" border="1">

            <thead>
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2">Learner</th>
                    <?php for ($d=1; $d<=31; $d++): ?>
                        <th><?php echo $d; ?></th>
                    <?php endfor; ?>
                    <th rowspan="2">P</th>
                    <th rowspan="2">A</th>
                    <th rowspan="2">T</th>
                </tr>
                <tr></tr>
            </thead>

            <tbody>
                <?php $i=1; foreach ($students_in_section as $stu): ?>
                <?php
                    $sid = $stu['student_id'];
                    $row = isset($daily_att[$sid]) ? $daily_att[$sid] : [];
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td style="text-align:left;"><?php echo htmlspecialchars($stu['fullname']); ?></td>

                    <?php
                    $present=0; $absent=0; $tardy=0;

                    for ($d=1; $d<=31; $d++):
                        $val = isset($row[$d]) ? $row[$d] : "";
                        if ($val === 'P') $present++;
                        if ($val === 'A') $absent++;
                        if ($val === 'TU' || $val === 'TL') $tardy++;
                    ?>
                    <td class="att-cell">
                        <select name="att[<?php echo $sid; ?>][<?php echo $d; ?>]">
                            <option value=""  <?php if ($val=="") echo "selected"; ?>></option>
                            <option value="P" <?php if ($val=="P") echo "selected"; ?>>P</option>
                            <option value="A" <?php if ($val=="A") echo "selected"; ?>>X</option>
                            <option value="TU"<?php if ($val=="TU") echo "selected"; ?>>TU</option>
                            <option value="TL"<?php if ($val=="TL") echo "selected"; ?>>TL</option>
                        </select>
                    </td>
                    <?php endfor; ?>

                    <td><?php echo $present; ?></td>
                    <td><?php echo $absent; ?></td>
                    <td><?php echo $tardy; ?></td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <br>
        <button type="submit" name="save_attendance">Save Attendance</button>

    </form>
</div>
<?php endif; ?>

</div>

</body>
</html>