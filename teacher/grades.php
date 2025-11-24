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

// --- 1. Get Context from URL (REQUIRED) ---
// Note: POST values override GET values after a submission, keeping the context.
$selected_section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : (isset($_POST['section_id']) ? (int)$_POST['section_id'] : 0);
$selected_subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : (isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0);
$selected_academic_year = "2025-2026"; // System default, typically based on current school year

if ($selected_section_id === 0 || $selected_subject_id === 0) {
    // If context is missing, redirect back to the class list (teacher_classes.php)
    header('Location: teacher_classes.php'); 
    exit; 
}

$message = "";
$message_status = "";
$save_period = isset($_POST['grading_period']) ? trim($_POST['grading_period']) : null;


// --- 2. Handle POST Request (Saving Grades) ---
if (isset($_POST['save_grades']) && $save_period) {
    
    if (!empty($_POST['grade']) && is_array($_POST['grade'])) {
        $ok_count = 0;
        $grade_status_temp = "";
        
        foreach ($_POST['grade'] as $student_id => $grade_value) {
            $grade_value = trim($grade_value);
            if ($grade_value === "") continue;
            
            if (!is_numeric($grade_value) || (float)$grade_value < 60 || (float)$grade_value > 100) {
                $message = "⚠️ Grade for Student ID {$student_id} is invalid (must be between 60 and 100).";
                $message_status = "err";
                $grade_status_temp = "err"; 
                continue; 
            }
            
            $saved = $teacherdb->saveGrade(
                (int)$student_id, (int)$selected_section_id, (int)$selected_subject_id, (int)$teacher_id, $save_period, (float)$grade_value
            );
            if ($saved) $ok_count++;
        }
        
        if ($grade_status_temp !== "err") {
            $message = ($ok_count > 0) ? "✅ Saved {$ok_count} grade(s) for {$save_period} successfully." : "⚠️ No valid grades were submitted.";
            $message_status = ($ok_count > 0) ? "ok" : "err";
        }
    } else {
        $message = "⚠️ No grades submitted.";
        $message_status = "err";
    }
}

// --- 3. Load All Necessary Data for Display ---

// 🔑 Fetches specific class info for display (Implemented in teacher_functions.php)
$class_info = $teacherdb->getClassInfo($selected_section_id, $selected_subject_id, $teacher_id);

// Fetches all students enrolled in the section
$students_in_section = $teacherdb->getSectionStudents($selected_section_id, $selected_academic_year);

// 🔑 Fetches all existing grades for this class across all 5 periods
$all_grades_for_class = $teacherdb->getAllGradesForClass($selected_section_id, $selected_subject_id);

// Define periods for column headers
$periods = ['1st Qtr', '2nd Qtr', '3rd Qtr', '4th Qtr', 'Final'];

// Set the currently active period for input visibility. If POST just happened, use $save_period.
// Otherwise, default to the 1st Qtr.
$active_input_period = $save_period ?: $periods[0];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Grade Sheet: <?php echo htmlspecialchars($class_info['subject_name'] ?? 'Loading...'); ?></title>
    <link rel="stylesheet" href="teacher_styles.css"> 
    <style>
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .grade-sheet-container { overflow-x: auto; margin-top: 15px; }
        .grades-table { width: 100%; min-width: 800px; border-collapse: collapse; font-size: 0.9em; }
        .grades-table th, .grades-table td { border: 1px solid #ddd; padding: 8px; text-align: center; white-space: nowrap; }
        .grades-table th { background: #44010b; color: white; position: sticky; top: 0; }
        .grades-table thead th:nth-child(1), .grades-table thead th:nth-child(2) { text-align: left; background: #333; color: white; }
        .grades-table input[type="number"] { width: 60px; padding: 3px; border: 1px solid #ccc; text-align: center; }
        .grades-table td:nth-child(2) { text-align: left; }
        .selection-bar { display: flex; gap: 20px; align-items: center; margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 5px;}
        .selection-bar label { font-weight: bold; }
        .selection-bar select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1; }
        .selection-bar button { flex-grow: 1; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="card">
        <a href="teacher_classes.php">← Back to Class List</a>
        <h2>Grading Sheet</h2>
        
        <p>
            **Class:** <strong><?php echo htmlspecialchars($class_info['section_name']); ?></strong> (G<?php echo htmlspecialchars($class_info['grade_level'] . $class_info['section_letter']); ?>)
            | **Subject:** <strong><?php echo htmlspecialchars($class_info['subject_name']); ?></strong>
            | **A.Y.:** <strong><?php echo htmlspecialchars($selected_academic_year); ?></strong>
        </p>

        <?php if (isset($message_status) && $message !== ""): ?>
            <div class="message <?php echo $message_status; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($students_in_section)): ?>
        
        <form method="post" action="grades.php">
            
            <div class="selection-bar">
                <label for="grading_period">Encode Grades for Period:</label>
                <select name="grading_period" id="grading_period" required>
                    <?php foreach ($periods as $p): ?>
                        <option value="<?php echo $p; ?>" <?php if ($active_input_period === $p) echo 'selected'; ?>><?php echo $p; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="save_grades" style="width: auto;">Save Grades</button>
            </div>

            <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($selected_section_id); ?>">
            <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($selected_subject_id); ?>">
            <input type="hidden" name="academic_year" value="<?php echo htmlspecialchars($selected_academic_year); ?>">

            <div class="grade-sheet-container">
                <table class="grades-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <?php foreach ($periods as $p): ?>
                                <th><?php echo $p; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $row_num = 1; foreach ($students_in_section as $stu): ?>
                            <?php $sid = $stu['student_id']; ?>
                            <tr>
                                <td><?php echo $row_num++; ?></td>
                                <td style="text-align: left;"><?php echo htmlspecialchars($stu['fullname']); ?></td>
                                
                                <?php foreach ($periods as $p): ?>
                                    <td style="text-align: center;">
                                        <?php 
                                        // Grade value for this student and period
                                        $grade_value = $all_grades_for_class[$sid][$p] ?? '';
                                        
                                        // Show input field ONLY for the currently selected period
                                        if ($active_input_period === $p): 
                                        ?>
                                            <input type="number" 
                                                   name="grade[<?php echo $sid; ?>]" 
                                                   step="0.01" min="60" max="100" 
                                                   value="<?php echo htmlspecialchars($grade_value); ?>">
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($grade_value) ?: '-'; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p style="margin-top: 15px; font-size: 0.9em;">
                **Note:** The **input field** is only visible in the column corresponding to the selected Grading Period for bulk entry. All other periods display the saved value.
            </p>
        </form>
        
        <?php else: ?>
            <p>No students enrolled in this section for the selected academic year (<?php echo htmlspecialchars($selected_academic_year); ?>).</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>