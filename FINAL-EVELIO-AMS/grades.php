<?php
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ header('Location: index.php'); exit; }

$db_available = false;
$crud = null;

try {
    include "pdo_functions.php";
    $crud = new pdoCRUD();
    $db_available = true;
} catch(Exception $e) {
    $db_available = false;
}

$loads = [];
$selected = (int)($_GET['load_id'] ?? 0);
$enrolled = [];
$selectedLoad = null;
$gradeMap = [];

if($db_available && $crud) {
    try {
        $acc = $crud->getAccountById($_SESSION['account_id']);
        $loads = $crud->getTeacherLoads($acc['person_id']);
        $enrolled = $selected ? $crud->getEnrollmentsByLoad($selected) : [];
        
        if($selected) {
            foreach($loads as $l) {
                if((int)$l['id'] === $selected) {
                    $selectedLoad = $l;
                    break;
                }
            }
        }
        
        if($selected) {
            $grades = $crud->getGradesForLoad($selected);
            foreach($grades as $g) {
                $gradeMap[(int)$g['enrollment_id']] = $g;
            }
        }
    } catch(Exception $e) {
        $db_available = false;
    }
}

if(!$db_available) {
    $loads = [
        ['id' => 1, 'subject_code' => 'MATH101', 'subject_name' => 'Mathematics 7', 'section_name' => 'Einstein'],
        ['id' => 2, 'subject_code' => 'MATH102', 'subject_name' => 'Mathematics 8', 'section_name' => 'Newton'],
        ['id' => 3, 'subject_code' => 'MATH201', 'subject_name' => 'Pre-Calculus', 'section_name' => 'Galileo']
    ];
    
    if($selected) {
        foreach($loads as $l) {
            if((int)$l['id'] === $selected) {
                $selectedLoad = $l;
                break;
            }
        }
        
        $enrolled = [
            ['id' => 1, 'enrollment_id' => 101, 'first_name' => 'Juan', 'family_name' => 'Dela Cruz'],
            ['id' => 2, 'enrollment_id' => 102, 'first_name' => 'Maria', 'family_name' => 'Santos'],
            ['id' => 3, 'enrollment_id' => 103, 'first_name' => 'Pedro', 'family_name' => 'Reyes'],
            ['id' => 4, 'enrollment_id' => 104, 'first_name' => 'Ana', 'family_name' => 'Garcia']
        ];
        
        $gradeMap = [
            101 => ['enrollment_id' => 101, 'q1' => 85, 'q2' => 88, 'q3' => 90, 'q4' => 87, 'grade' => 88],
            102 => ['enrollment_id' => 102, 'q1' => 92, 'q2' => 94, 'q3' => 91, 'q4' => 93, 'grade' => 93],
            103 => ['enrollment_id' => 103, 'q1' => 78, 'q2' => 80, 'q3' => '', 'q4' => '', 'grade' => ''],
            104 => ['enrollment_id' => 104, 'q1' => '', 'q2' => '', 'q3' => '', 'q4' => '', 'grade' => '']
        ];
    }
}

$page_title = 'Manage Grades';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'teacher_dashboard.php'],
    ['title' => 'Manage Grades', 'active' => true]
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
                        <h1 class="page-header-title">Manage Grades</h1>
                        <p class="page-header-subtitle">Enter and manage student grades for your subject loads.</p>
                    </div>
                </div>
            </div>
            
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">Select Subject Load</h3>
                </div>
                <div class="card-body">
                    <div class="form-row-3">
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
                    </div>
                </div>
            </div>
            
            <?php if($selected && $selectedLoad): ?>
            <div class="grid-stats mb-6">
                <div class="card stat-card">
                    <div class="stat-card-icon primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo htmlspecialchars($selectedLoad['subject_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="stat-card-label">Subject Code</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-card-icon accent">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="3" y1="9" x2="21" y2="9"></line>
                            <line x1="9" y1="21" x2="9" y2="9"></line>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo htmlspecialchars($selectedLoad['section_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="stat-card-label">Section</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-card-icon success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo count($enrolled); ?></div>
                    <div class="stat-card-label">Enrolled Students</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-card-icon warning">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo count(array_filter($gradeMap, function($g) { return !empty($g['grade']); })); ?></div>
                    <div class="stat-card-label">Graded</div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-between align-center flex-wrap gap-4">
                        <h3 class="card-title">Student Grades - <?php echo htmlspecialchars($selectedLoad['subject_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                        <button onclick="saveAllGrades()" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Save All Grades
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Student Name</th>
                                    <th style="width: 120px;">1st Quarter</th>
                                    <th style="width: 120px;">2nd Quarter</th>
                                    <th style="width: 120px;">3rd Quarter</th>
                                    <th style="width: 120px;">4th Quarter</th>
                                    <th style="width: 120px;">Final Grade</th>
                                    <th style="width: 100px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!$enrolled): ?>
                                <tr>
                                    <td colspan="8" class="text-center p-6">
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
                                <?php $i = 1; foreach($enrolled as $e): 
                                    $eid = (int)$e['enrollment_id'];
                                    $gradeData = $gradeMap[$eid] ?? [];
                                    $q1 = $gradeData['q1'] ?? '';
                                    $q2 = $gradeData['q2'] ?? '';
                                    $q3 = $gradeData['q3'] ?? '';
                                    $q4 = $gradeData['q4'] ?? '';
                                    $finalGrade = $gradeData['grade'] ?? '';
                                ?>
                                <tr data-eid="<?php echo $eid; ?>">
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <div class="d-flex align-center gap-3">
                                            <div class="avatar avatar-sm avatar-primary">
                                                <?php echo strtoupper(substr($e['first_name'] ?? 'S', 0, 1)); ?>
                                            </div>
                                            <span class="font-medium"><?php echo htmlspecialchars($e['family_name'] . ', ' . $e['first_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm grade-input" 
                                               data-eid="<?php echo $eid; ?>" data-period="q1"
                                               value="<?php echo htmlspecialchars($q1, ENT_QUOTES, 'UTF-8'); ?>"
                                               min="0" max="100" placeholder="--">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm grade-input" 
                                               data-eid="<?php echo $eid; ?>" data-period="q2"
                                               value="<?php echo htmlspecialchars($q2, ENT_QUOTES, 'UTF-8'); ?>"
                                               min="0" max="100" placeholder="--">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm grade-input" 
                                               data-eid="<?php echo $eid; ?>" data-period="q3"
                                               value="<?php echo htmlspecialchars($q3, ENT_QUOTES, 'UTF-8'); ?>"
                                               min="0" max="100" placeholder="--">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm grade-input" 
                                               data-eid="<?php echo $eid; ?>" data-period="q4"
                                               value="<?php echo htmlspecialchars($q4, ENT_QUOTES, 'UTF-8'); ?>"
                                               min="0" max="100" placeholder="--">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm grade-input final-grade" 
                                               data-eid="<?php echo $eid; ?>" data-period="final"
                                               value="<?php echo htmlspecialchars($finalGrade, ENT_QUOTES, 'UTF-8'); ?>"
                                               min="0" max="100" placeholder="--" readonly style="background-color: var(--color-gray-50); font-weight: 600;">
                                    </td>
                                    <td>
                                        <?php 
                                        $status = '';
                                        $badgeClass = 'badge-secondary';
                                        if($finalGrade !== '') {
                                            if((float)$finalGrade >= 75) {
                                                $status = 'Passed';
                                                $badgeClass = 'badge-success';
                                            } else {
                                                $status = 'Failed';
                                                $badgeClass = 'badge-danger';
                                            }
                                        } else {
                                            $status = 'Pending';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> status-badge" data-eid="<?php echo $eid; ?>"><?php echo $status; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if($enrolled): ?>
                <div class="card-footer">
                    <div class="d-flex justify-end gap-3">
                        <a href="teacher_dashboard.php" class="btn btn-secondary">Cancel</a>
                        <button onclick="saveAllGrades()" class="btn btn-success">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Save All Grades
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php elseif(!$selected): ?>
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        <h4 class="mb-2">Select a Subject Load</h4>
                        <p class="text-muted">Choose a subject load from the dropdown above to view and manage student grades.</p>
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
.justify-end { justify-content: flex-end; }
.align-center { align-items: center; }
.flex-wrap { flex-wrap: wrap; }
.gap-4 { gap: var(--spacing-4); }
.gap-3 { gap: var(--spacing-3); }
.text-center { text-align: center; }
.text-muted { color: var(--color-text-muted); }
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

.grade-input {
    text-align: center;
    font-weight: var(--font-weight-medium);
}

.grade-input:focus {
    border-color: var(--color-accent);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}
</style>

<script>
function selectLoad() {
    const id = document.getElementById('load').value;
    window.location.href = 'grades.php?load_id=' + id;
}

document.querySelectorAll('.grade-input:not(.final-grade)').forEach(input => {
    input.addEventListener('change', function() {
        calculateFinalGrade(this.dataset.eid);
    });
});

function calculateFinalGrade(eid) {
    const row = document.querySelector(`tr[data-eid="${eid}"]`);
    if (!row) return;
    
    const q1 = parseFloat(row.querySelector('[data-period="q1"]').value) || 0;
    const q2 = parseFloat(row.querySelector('[data-period="q2"]').value) || 0;
    const q3 = parseFloat(row.querySelector('[data-period="q3"]').value) || 0;
    const q4 = parseFloat(row.querySelector('[data-period="q4"]').value) || 0;
    
    const count = [q1, q2, q3, q4].filter(v => v > 0).length;
    const finalGrade = count > 0 ? Math.round((q1 + q2 + q3 + q4) / count) : '';
    
    const finalInput = row.querySelector('.final-grade');
    finalInput.value = finalGrade;
    
    const statusBadge = document.querySelector(`.status-badge[data-eid="${eid}"]`);
    if (finalGrade !== '') {
        if (finalGrade >= 75) {
            statusBadge.textContent = 'Passed';
            statusBadge.className = 'badge badge-success status-badge';
        } else {
            statusBadge.textContent = 'Failed';
            statusBadge.className = 'badge badge-danger status-badge';
        }
    } else {
        statusBadge.textContent = 'Pending';
        statusBadge.className = 'badge badge-secondary status-badge';
    }
}

function saveAllGrades() {
    const rows = document.querySelectorAll('tr[data-eid]');
    const grades = [];
    
    rows.forEach(row => {
        const eid = row.dataset.eid;
        const finalGrade = row.querySelector('.final-grade').value;
        if (finalGrade) {
            grades.push({ enrollment_id: eid, grade: finalGrade });
        }
    });
    
    if (grades.length === 0) {
        alert('No grades to save. Please enter at least one grade.');
        return;
    }
    
    let savedCount = 0;
    let errorCount = 0;
    
    grades.forEach(g => {
        fetch('saveGrade.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'enrollment_id=' + g.enrollment_id + '&grade=' + encodeURIComponent(g.grade)
        })
        .then(r => r.json())
        .then(j => {
            if (j.success) savedCount++;
            else errorCount++;
            
            if (savedCount + errorCount === grades.length) {
                if (errorCount === 0) {
                    alert('All grades saved successfully!');
                } else {
                    alert('Saved ' + savedCount + ' grades. ' + errorCount + ' failed.');
                }
            }
        })
        .catch(() => {
            errorCount++;
            if (savedCount + errorCount === grades.length) {
                alert('Saved ' + savedCount + ' grades. ' + errorCount + ' failed.');
            }
        });
    });
}
</script>

<?php include "includes/footer.php"; ?>
