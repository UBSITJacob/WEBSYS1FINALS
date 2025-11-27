<?php
if (empty($students_in_section) || !$selected_student_id) return;

$selected_student = null;
foreach ($students_in_section as $stu) {
    if ($stu['student_id'] == $selected_student_id) {
        $selected_student = $stu;
        break;
    }
}
if (!$selected_student) return;

$sid = $selected_student_id;
$row = isset($daily_att[$sid]) ? $daily_att[$sid] : [];

// compute summary
$presentCount = $absentCount = $tardyCount = 0;
foreach ($row as $status) {
    if ($status == "P") $presentCount++;
    elseif ($status == "A") $absentCount++;
    elseif ($status == "TU" || $status == "TL") $tardyCount++;
}

$firstDayString = sprintf("%04d-%02d-01", $selected_year, $selected_month);
$daysInMonth    = (int)date('t', strtotime($firstDayString));
$firstDayDow    = (int)date('w', strtotime($firstDayString)); // Sunday = 0

$monthLabel = $monthNames[$selected_month];

// load stored layout from session
$layoutKey = $teacher_id . '_' . $selected_section_id . '_' . $selected_subject_id . '_' .
            $selected_year . '_' . $selected_month . '_' . $sid;

$storedLayout = isset($_SESSION['calendar_layout'][$layoutKey]) ?
                $_SESSION['calendar_layout'][$layoutKey] : null;

$layoutExists = is_array($storedLayout);
?>

<div class="card">
    <h3>Daily Attendance — <?php echo htmlspecialchars($selected_student['fullname']); ?></h3>

    <form method="post" action="attendance.php">
        <input type="hidden" name="section_id" value="<?php echo $selected_section_id; ?>">
        <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">
        <input type="hidden" name="attendance_year" value="<?php echo $selected_year; ?>">
        <input type="hidden" name="attendance_month" value="<?php echo $selected_month; ?>">
        <input type="hidden" name="student_id" value="<?php echo $selected_student_id; ?>">

        <div class="calendar-wrapper">
            <table class="calendar-table">
                <thead>
                    <tr>
                        <th>Sun</th><th>Mon</th><th>Tue</th>
                        <th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                    </tr>
                </thead>
                <tbody>

<?php
$cellIndex = 0;
for ($week = 0; $week < 6; $week++):
    echo "<tr>";
    for ($dow = 0; $dow <= 6; $dow++):
        $cellIndex++;
        $defaultDayNum = "";
        $val = "";

        if ($layoutExists) {
            $dayForCell = isset($storedLayout[$cellIndex]) ?
                          trim($storedLayout[$cellIndex]) : "";
            if ($dayForCell !== "") {
                $defaultDayNum = $dayForCell;
                $val = isset($row[$dayForCell]) ? $row[$dayForCell] : "";
            }
        } else {
            $offset = $cellIndex - 1;
            if ($offset >= $firstDayDow && $offset < $firstDayDow + $daysInMonth) {
                $dayForCell = $offset - $firstDayDow + 1;
                $defaultDayNum = $dayForCell;
                $val = isset($row[$dayForCell]) ? $row[$dayForCell] : "";
            }
        }
?>

<td class="calendar-day">
    <div class="day-number">
        <input type="number" name="day_num[<?php echo $cellIndex; ?>]"
               value="<?php echo $defaultDayNum; ?>" min="1" max="31">
    </div>
    <div class="day-status">
        <select class="attendance-select"
                name="att[<?php echo $sid; ?>][<?php echo $cellIndex; ?>]">
            <option value=""  <?php if ($val=="") echo "selected"; ?>></option>
            <option value="P" <?php if ($val=="P") echo "selected"; ?>>P</option>
            <option value="A" <?php if ($val=="A") echo "selected"; ?>>X</option>
            <option value="TU"<?php if ($val=="TU")echo "selected"; ?>>TU</option>
            <option value="TL"<?php if ($val=="TL")echo "selected"; ?>>TL</option>
        </select>
    </div>
</td>

<?php
    endfor;
    echo "</tr>";
endfor;
?>

                </tbody>
            </table>
        </div>

        <div class="attendance-summary">
            <strong>Summary:</strong>
            <span>Present: <span id="countPresent"><?php echo $presentCount; ?></span></span>
            <span>Absent: <span id="countAbsent"><?php echo $absentCount; ?></span></span>
            <span>Late: <span id="countTardy"><?php echo $tardyCount; ?></span></span>
        </div>

        <div class="calendar-controls">
            <button type="button" id="clearDaysBtn">Clear All Days</button>
            <button type="button" id="clearAttendanceBtn">Clear All Attendance</button>
            <button type="submit" name="save_attendance">Save Attendance</button>
        </div>
    </form>
</div>

<script>
// JS for coloring, clearing, and summary updates
document.addEventListener('DOMContentLoaded', function() {

    const dayInputs  = document.querySelectorAll('.calendar-day input[name^="day_num"]');
    const selects    = document.querySelectorAll('.attendance-select');

    function updateSummary() {
        let P=0, A=0, T=0;
        selects.forEach(sel=>{
            if(sel.value=="P") P++;
            else if(sel.value=="A") A++;
            else if(sel.value=="TU"||sel.value=="TL") T++;
        });
        document.getElementById('countPresent').textContent=P;
        document.getElementById('countAbsent').textContent=A;
        document.getElementById('countTardy').textContent=T;
    }

    function colorize() {
        selects.forEach(sel=>{
            sel.className = "attendance-select attendance-" + (sel.value || "empty");
        });
    }

    document.getElementById('clearDaysBtn').onclick = ()=>{
        dayInputs.forEach(i=> i.value="");
    };
    document.getElementById('clearAttendanceBtn').onclick = ()=>{
        selects.forEach(s=> s.value="");
        updateSummary();
        colorize();
    };

    selects.forEach(s=>{
        s.onchange = ()=>{updateSummary(); colorize();}
    });

    updateSummary();
    colorize();
});
</script>
