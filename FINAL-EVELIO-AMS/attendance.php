<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ header('Location: index.php'); exit; }

$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$loads = $crud->getTeacherLoads($acc['person_id']);
$selected = (int)($_GET['load_id'] ?? 0);
$date = $_GET['date'] ?? date('Y-m-d');
$students = $selected ? $crud->getEnrollmentsByLoad($selected) : [];

$selectedLoad = null;
if($selected) {
    foreach($loads as $l) {
        if((int)$l['id'] === $selected) {
            $selectedLoad = $l;
            break;
        }
    }
}

$attendanceMap = [];
if($selected) {
    $att = $crud->getAttendanceForLoadAndDate($selected, $date);
    foreach($att as $a) {
        $attendanceMap[(int)$a['student_id']] = $a['status'];
    }
}

$presentCount = 0;
$absentCount = 0;
$tardyCount = 0;
$unmarkedCount = 0;
foreach($students as $s) {
    $status = $attendanceMap[(int)$s['id']] ?? '';
    if($status === 'present') $presentCount++;
    elseif($status === 'absent') $absentCount++;
    elseif($status === 'tardy') $tardyCount++;
    else $unmarkedCount++;
}

if(isset($_POST['save_attendance'])) {
    foreach($students as $s) {
        $sid = (int)$s['id'];
        $status = $_POST['status_' . $sid] ?? '';
        if($status) {
            $crud->markAttendance($sid, $selected, $date, $status);
        }
    }
    header('Location: attendance.php?load_id=' . $selected . '&date=' . urlencode($date) . '&saved=1');
    exit;
}

$page_title = 'Attendance';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'teacher_dashboard.php'],
    ['title' => 'Attendance', 'active' => true]
];
?>
<?php include "includes/header.php"; ?>
<div class="app-layout">
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <main class="main-content">
            <div class="page-header">
                <div class="page-header-row">
                    <div>
                        <h1 class="page-header-title">Attendance</h1>
                        <p class="page-header-subtitle">Mark and manage student attendance for your classes.</p>
                    </div>
                </div>
            </div>
            
            <?php if(isset($_GET['saved'])): ?>
            <div class="alert alert-success mb-6">
                <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div class="alert-content">
                    <div class="alert-title">Success!</div>
                    Attendance has been saved successfully.
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">Select Class & Date</h3>
                </div>
                <div class="card-body">
                    <div class="form-row-2">
                        <div class="form-group mb-0">
                            <label class="form-label">Subject Load</label>
                            <select id="load" class="form-control" onchange="selectLoad()">
                                <option value="0">-- Select Subject Load --</option>
                                <?php foreach($loads as $l): ?>
                                <option value="<?php echo (int)$l['id']; ?>" <?php echo $selected == (int)$l['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(($l['subject_code'] ?? '') . ' - ' . ($l['subject_name'] ?? '') . ' (' . ($l['section_name'] ?? '') . ')', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label class="form-label">Date</label>
                            <input type="date" id="date" class="form-control" value="<?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?>" onchange="selectLoad()">
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if($selected && $selectedLoad): ?>
            <div class="grid-stats mb-6">
                <div class="card stat-card">
                    <div class="stat-card-icon success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $presentCount; ?></div>
                    <div class="stat-card-label">Present</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-card-icon danger">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $absentCount; ?></div>
                    <div class="stat-card-label">Absent</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-card-icon warning">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $tardyCount; ?></div>
                    <div class="stat-card-label">Tardy</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-card-icon primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $unmarkedCount; ?></div>
                    <div class="stat-card-label">Unmarked</div>
                </div>
            </div>
            
            <form method="post" id="attendanceForm">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-between align-center flex-wrap gap-4">
                            <div>
                                <h3 class="card-title"><?php echo htmlspecialchars($selectedLoad['subject_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="text-sm text-muted mb-0"><?php echo htmlspecialchars($selectedLoad['section_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> - <?php echo date('F j, Y', strtotime($date)); ?></p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" onclick="markAll('present')" class="btn btn-sm btn-success">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    All Present
                                </button>
                                <button type="button" onclick="markAll('absent')" class="btn btn-sm btn-danger">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    All Absent
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>Student Name</th>
                                        <th style="width: 120px;" class="text-center">Present</th>
                                        <th style="width: 120px;" class="text-center">Absent</th>
                                        <th style="width: 120px;" class="text-center">Tardy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!$students): ?>
                                    <tr>
                                        <td colspan="5" class="text-center p-6">
                                            <div class="empty-state-inline">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-3">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <line x1="23" y1="11" x2="17" y2="11"></line>
                                                </svg>
                                                <p class="text-muted mb-0">No students enrolled in this subject</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php $i = 1; foreach($students as $s): 
                                        $sid = (int)$s['id'];
                                        $currentStatus = $attendanceMap[$sid] ?? '';
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td>
                                            <div class="d-flex align-center gap-3">
                                                <div class="avatar avatar-sm avatar-primary">
                                                    <?php echo strtoupper(substr($s['first_name'] ?? 'S', 0, 1)); ?>
                                                </div>
                                                <span class="font-medium"><?php echo htmlspecialchars($s['family_name'] . ', ' . $s['first_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <label class="attendance-radio">
                                                <input type="radio" name="status_<?php echo $sid; ?>" value="present" class="attendance-input" data-status="present" <?php echo $currentStatus === 'present' ? 'checked' : ''; ?>>
                                                <span class="attendance-check success">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="attendance-radio">
                                                <input type="radio" name="status_<?php echo $sid; ?>" value="absent" class="attendance-input" data-status="absent" <?php echo $currentStatus === 'absent' ? 'checked' : ''; ?>>
                                                <span class="attendance-check danger">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                </span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <label class="attendance-radio">
                                                <input type="radio" name="status_<?php echo $sid; ?>" value="tardy" class="attendance-input" data-status="tardy" <?php echo $currentStatus === 'tardy' ? 'checked' : ''; ?>>
                                                <span class="attendance-check warning">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <polyline points="12 6 12 12 16 14"></polyline>
                                                    </svg>
                                                </span>
                                            </label>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if($students): ?>
                    <div class="card-footer">
                        <div class="d-flex justify-between align-center flex-wrap gap-3">
                            <p class="text-sm text-muted mb-0">
                                Total: <?php echo count($students); ?> students
                            </p>
                            <div class="d-flex gap-3">
                                <a href="teacher_dashboard.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" name="save_attendance" class="btn btn-primary">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    Save Attendance
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
            <?php elseif(!$selected): ?>
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                        </div>
                        <h4 class="mb-2">Select a Class</h4>
                        <p class="text-muted">Choose a subject load and date from the options above to mark attendance.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<style>
.mb-6 { margin-bottom: var(--spacing-6); }
.mb-3 { margin-bottom: var(--spacing-3); }
.mb-2 { margin-bottom: var(--spacing-2); }
.mb-0 { margin-bottom: 0; }
.p-6 { padding: var(--spacing-6); }
.p-0 { padding: 0; }
.d-flex { display: flex; }
.justify-between { justify-content: space-between; }
.align-center { align-items: center; }
.flex-wrap { flex-wrap: wrap; }
.gap-4 { gap: var(--spacing-4); }
.gap-3 { gap: var(--spacing-3); }
.gap-2 { gap: var(--spacing-2); }
.text-center { text-align: center; }
.text-muted { color: var(--color-text-muted); }
.text-sm { font-size: var(--font-size-sm); }
.font-medium { font-weight: var(--font-weight-medium); }

.empty-state {
    text-align: center;
    padding: var(--spacing-12);
}

.empty-state-icon {
    color: var(--color-text-muted);
    margin-bottom: var(--spacing-4);
}

.empty-state-inline {
    padding: var(--spacing-8);
}

.attendance-radio {
    display: inline-flex;
    cursor: pointer;
}

.attendance-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.attendance-check {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    border: 2px solid var(--color-border);
    background-color: var(--color-white);
    transition: all var(--transition-fast);
}

.attendance-check svg {
    opacity: 0.3;
    transition: opacity var(--transition-fast);
}

.attendance-check.success {
    color: var(--color-success);
}

.attendance-check.danger {
    color: var(--color-danger);
}

.attendance-check.warning {
    color: var(--color-warning);
}

.attendance-input:checked + .attendance-check.success {
    background-color: var(--color-success);
    border-color: var(--color-success);
    color: white;
}

.attendance-input:checked + .attendance-check.danger {
    background-color: var(--color-danger);
    border-color: var(--color-danger);
    color: white;
}

.attendance-input:checked + .attendance-check.warning {
    background-color: var(--color-warning);
    border-color: var(--color-warning);
    color: white;
}

.attendance-input:checked + .attendance-check svg {
    opacity: 1;
}

.attendance-radio:hover .attendance-check {
    transform: scale(1.1);
}
</style>

<script>
function selectLoad() {
    const id = document.getElementById('load').value;
    const d = document.getElementById('date').value;
    window.location.href = 'attendance.php?load_id=' + id + '&date=' + encodeURIComponent(d);
}

function markAll(status) {
    document.querySelectorAll(`.attendance-input[value="${status}"]`).forEach(input => {
        input.checked = true;
    });
}
</script>

<?php include "includes/footer.php"; ?>
