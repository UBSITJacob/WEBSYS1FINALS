<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ exit; }

$pdo = new pdoCRUD();
$student_id = (int)($_GET['student_id'] ?? 0);
$year = (int)($_GET['year'] ?? date('Y'));
if(!$student_id){ echo '<div class="text-danger">Invalid student</div>'; exit; }
$student = $pdo->getStudentById($student_id);
if(!$student){ echo '<div class="text-danger">Student not found</div>'; exit; }

$grades = $pdo->getStudentGrades($student_id);
$grades = array_values(array_filter($grades, function($g) use($year){ return (int)($g['school_year'] ?? 0) === (int)$year; }));

$gwa = null;
if(!empty($grades)){
    $sum = 0; $count = 0;
    foreach($grades as $g){
        $val = (float)($g['grade'] ?? 0);
        if($val > 0){ $sum += $val; $count++; }
    }
    if($count > 0){ $gwa = round($sum / $count, 2); }
}

$attendance = [];
for($m=1;$m<=12;$m++){
    $monthAtt = $pdo->getAttendanceByStudent($student_id,$year,$m);
    if($monthAtt){ $attendance = array_merge($attendance, $monthAtt); }
}

// --- NEW: Calculate Attendance Summary ---
$attendance_summary = [
    'present' => 0,
    'tardy' => 0,
    'absent' => 0,
    'other' => 0,
];

foreach($attendance as $a) {
    $status = strtolower($a['status'] ?? '');
    if (isset($attendance_summary[$status])) {
        $attendance_summary[$status]++;
    } else {
        $attendance_summary['other']++;
    }
}
// ------------------------------------------

function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<div class="grid grid-1" style="gap: 16px;">
    
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Report Card (<?php echo (int)$year; ?>)</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Section</th>
                            <th>Q1</th>
                            <th>Q2</th>
                            <th>Q3</th>
                            <th>Q4</th>
                            <th>AVERAGE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($grades)): ?>
                        <tr><td colspan="7" class="text-center text-muted p-6">No grades recorded for this year</td></tr>
                        <?php else: foreach($grades as $g): ?>
                        <tr>
                            <td class="font-medium"><?php echo esc($g['subject_name']); ?></td>
                            <td><?php echo esc($g['section_name']); ?></td>
                            <td><?php echo esc($g['q1']); ?></td>
                            <td><?php echo esc($g['q2']); ?></td>
                            <td><?php echo esc($g['q3']); ?></td>
                            <td><?php echo esc($g['q4']); ?></td>
                            <td><span class="badge badge-secondary"><?php echo esc($g['grade']); ?></span></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex" style="justify-content: space-between; align-items: center; width: 100%;">
                <span class="text-sm text-muted">General Weighted Average</span>
                <span>
                    <?php if($gwa !== null): ?>
                        <span class="badge badge-primary"><?php echo number_format($gwa, 2); ?></span>
                    <?php else: ?>
                        <span class="text-muted">N/A</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Attendance Summary (<?php echo (int)$year; ?>)</h4>
        </div>
        <div class="card-body">
            
            <div class="attendance-summary">
                <div class="stat-card">
                    <span class="stat-label">Days Present</span>
                    <span class="stat-value text-success"><?php echo (int)$attendance_summary['present']; ?></span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Tardy Count</span>
                    <span class="stat-value text-warning"><?php echo (int)$attendance_summary['tardy']; ?></span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Days Absent</span>
                    <span class="stat-value text-danger"><?php echo (int)$attendance_summary['absent']; ?></span>
                </div>
                <?php if ($attendance_summary['other'] > 0): ?>
                <div class="stat-card">
                    <span class="stat-label">Other Statuses</span>
                    <span class="stat-value text-muted"><?php echo (int)$attendance_summary['other']; ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>