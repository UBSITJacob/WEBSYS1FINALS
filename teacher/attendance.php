<?php
require_once 'attendance_logic.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Calendar</title>
    <link rel="stylesheet" href="teacher_styles.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content attendance-page">

<div class="card">
    <h2>Daily Attendance — Calendar View</h2>

    <?php if ($message): ?>
        <div class="message <?php echo $message_status; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- FILTER FORM GOES HERE -->
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
                        <?php echo $load['section_name'] .
                            " (G" . $load['grade_level'] . $load['section_letter'] . ")"; ?>
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

        <?php if (!empty($students_in_section)): ?>
        <div class="form-row">
            <label>Learner:</label>
            <select name="student_id">
                <option value="">-- Select Learner --</option>
                <?php foreach ($students_in_section as $stu): ?>
                    <option value="<?php echo $stu['student_id']; ?>"
                        <?php if ($selected_student_id == $stu['student_id']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($stu['fullname']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div class="form-row">
            <label>Year:</label>
            <input type="number" name="attendance_year" value="<?php echo $selected_year; ?>">
        </div>

        <div class="form-row">
            <label>Month:</label>
            <select name="attendance_month">
                <?php foreach ($monthNames as $m => $label): ?>
                    <option value="<?php echo $m; ?>"
                        <?php if ((int)$selected_month === $m) echo 'selected'; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Load Calendar</button>
    </form>
</div>

<!-- LOAD THE CALENDAR OUTPUT -->
<?php include 'attendance_view.php'; ?>

</div> <!-- Main Content -->

</body>
</html>
