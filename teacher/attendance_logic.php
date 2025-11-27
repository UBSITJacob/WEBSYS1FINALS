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

$selected_student_id = 0;

$monthNames = [
    1  => 'January',
    2  => 'February',
    3  => 'March',
    4  => 'April',
    5  => 'May',
    6  => 'June',
    7  => 'July',
    8  => 'August',
    9  => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December',
];

// ---------------------------------------------
// SAVE DAILY ATTENDANCE
// ---------------------------------------------
if (isset($_POST['save_attendance'])) {

    $section_id = (int)$_POST['section_id'];
    $subject_id = (int)$_POST['subject_id'];
    $year       = (int)$_POST['attendance_year'];
    $month      = (int)$_POST['attendance_month'];
    $selected_student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;

    $selected_section_id = $section_id;
    $selected_subject_id = $subject_id;
    $selected_year       = $year;
    $selected_month      = $month;

    if ($section_id && $subject_id && $year && $month && $selected_student_id) {

        // Save the current layout of day numbers into the session
        $layoutKey = $teacher_id . '_' . $section_id . '_' . $subject_id . '_' .
                     $year . '_' . $month . '_' . $selected_student_id;
        $_SESSION['calendar_layout'][$layoutKey] =
            (isset($_POST['day_num']) && is_array($_POST['day_num']))
                ? $_POST['day_num']
                : [];

        if (!empty($_POST['att']) && is_array($_POST['att'])) {

            $dayNumOverrides = isset($_POST['day_num']) && is_array($_POST['day_num'])
                ? $_POST['day_num']
                : [];

            foreach ($_POST['att'] as $student_id => $cells) {
                foreach ($cells as $cellIndex => $status) {

                    if ($status === "") continue;

                    // cellIndex is 1..42; day_num[cellIndex] tells us which day-of-month this cell represents
                    $actualDay = isset($dayNumOverrides[$cellIndex])
                        ? (int)$dayNumOverrides[$cellIndex]
                        : 0;

                    if ($actualDay < 1 || $actualDay > 31) {
                        // skip invalid or blank day entries
                        continue;
                    }

                    $date = sprintf("%04d-%02d-%02d", $year, $month, $actualDay);

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

            // COMPUTE MONTHLY TOTALS (Present only = P)
            $students = $teacherdb->getSectionStudents($section_id, $selected_academic_year);
            $daily    = $teacherdb->getDailyAttendance($section_id, $subject_id, $year, $month);

            foreach ($students as $stu) {
                $sid = $stu['student_id'];
                $presentCount = 0;

                foreach ($daily as $row) {
                    if ($row['student_id'] == $sid && $row['status'] == 'P') {
                        $presentCount++;
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
        $message = "⚠️ Please select section, subject, year, month, and learner.";
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
if (isset($_GET['student_id'])) {
    $selected_student_id = (int)$_GET['student_id'];
}

$students_in_section = [];
$class_info = [];

if ($selected_section_id !== "") {
    $students_in_section = $teacherdb->getSectionStudents((int)$selected_section_id, $selected_academic_year);

    foreach ($loads as $load) {
        if ($load['section_id'] == $selected_section_id) {
            $class_info['section_label'] =
                $load['section_name'] . " (G" . $load['grade_level'] . $load['section_letter'] . ")";
        }
        if ($load['section_id'] == $selected_section_id && $load['subject_id'] == $selected_subject_id) {
            $class_info['subject_label'] = $load['subject_name'];
        }
    }
}

// Load daily attendance into [student_id][day_of_month] = status
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
