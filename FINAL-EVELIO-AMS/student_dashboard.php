<?php
include "session.php";
require_login();
if($_SESSION['role']!=='student'){ header('Location: index.php'); exit; }

$page_title = 'Student Dashboard';
$db_available = false;
$pdoC = null;
$pdo = null;

try {
    include "pdo_functions.php";
    $pdoC = new pdoCRUD();
    $db_available = $pdoC->isConnected();
} catch(Exception $e) {
    $db_available = false;
}

$acc = null;
$person = null;
$subjects_count = 0;
$section = '';
$strand = '';
$gwa = 0;
$attendance_present = 0;
$attendance_absent = 0;
$attendance_late = 0;
$attendance_total = 0;
$attendance_percentage = 0;

if($db_available && $pdoC) {
    try {
        $acc = $pdoC->getAccountById($_SESSION['account_id']);
        $person = $pdoC->getAccountPerson('student',$acc['person_id']);
        $strand = $person['strand'] ?? '';
        
        include "dbconfig.php";
        
        $stmt = $pdo->prepare("SELECT COUNT(*) c FROM enrollments WHERE student_id = :sid");
        $stmt->execute([':sid'=>$person['id']]);
        $subjects_count = (int)$stmt->fetch()['c'];
        
        if($person['advisory_section_id']){
            $s = $pdo->prepare("SELECT name FROM sections WHERE id = :id");
            $s->execute([':id'=>$person['advisory_section_id']]);
            $row = $s->fetch();
            $section = $row? $row['name'] : '';
        }
        
        $gradeStmt = $pdo->prepare("SELECT AVG(g.grade) as avg_grade FROM grades g 
            INNER JOIN enrollments e ON g.enrollment_id = e.id 
            WHERE e.student_id = :sid AND g.grade IS NOT NULL");
        $gradeStmt->execute([':sid'=>$person['id']]);
        $gwaResult = $gradeStmt->fetch();
        $gwa = $gwaResult && $gwaResult['avg_grade'] ? round($gwaResult['avg_grade'], 2) : 0;
        
        $attStmt = $pdo->prepare("SELECT status, COUNT(*) as cnt FROM attendance WHERE student_id = :sid GROUP BY status");
        $attStmt->execute([':sid'=>$person['id']]);
        while($attRow = $attStmt->fetch()) {
            if(strtolower($attRow['status']) === 'present') $attendance_present = (int)$attRow['cnt'];
            elseif(strtolower($attRow['status']) === 'absent') $attendance_absent = (int)$attRow['cnt'];
            elseif(strtolower($attRow['status']) === 'late') $attendance_late = (int)$attRow['cnt'];
        }
        $attendance_total = $attendance_present + $attendance_absent + $attendance_late;
        $attendance_percentage = $attendance_total > 0 ? round(($attendance_present / $attendance_total) * 100, 1) : 0;
    } catch(Exception $e) {
        $db_available = false;
    }
}

if(!$db_available) {
    $person = [
        'id' => 1,
        'lrn' => '123456789012',
        'first_name' => 'Juan',
        'family_name' => 'Dela Cruz',
        'middle_name' => 'Santos',
        'grade_level' => 'Grade 11',
        'strand' => 'STEM',
        'advisory_section_id' => 1
    ];
    $strand = 'STEM';
    $section = 'Einstein';
    $subjects_count = 8;
    $gwa = 88.5;
    $attendance_present = 45;
    $attendance_absent = 3;
    $attendance_late = 2;
    $attendance_total = 50;
    $attendance_percentage = 90.0;
}

$student_name = htmlspecialchars($person['first_name'].' '.$person['family_name'], ENT_QUOTES, 'UTF-8');
$student_initials = strtoupper(substr($person['first_name'],0,1) . substr($person['family_name'],0,1));

$breadcrumb = [
    ['title' => 'Dashboard', 'active' => true]
];

$announcements = [
    ['title' => 'Second Quarter Exam Schedule', 'date' => 'Dec 15-20, 2025', 'type' => 'warning'],
    ['title' => 'Christmas Break Announcement', 'date' => 'Dec 21, 2025 - Jan 2, 2026', 'type' => 'info'],
    ['title' => 'Report Card Distribution', 'date' => 'Jan 10, 2026', 'type' => 'primary']
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
                        <h1 class="page-header-title">Welcome back, <?php echo htmlspecialchars($person['first_name'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
                        <p class="page-header-subtitle">Here's your academic overview for this school year.</p>
                    </div>
                </div>
            </div>
            
            <div class="grid-2" style="margin-bottom: var(--spacing-6);">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-4" style="align-items: flex-start;">
                            <div class="avatar avatar-xl avatar-primary"><?php echo $student_initials; ?></div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 var(--spacing-2) 0; font-size: var(--font-size-xl);"><?php echo $student_name; ?></h3>
                                <div class="d-flex gap-4 flex-wrap" style="margin-top: var(--spacing-3);">
                                    <div>
                                        <span class="text-muted text-sm">LRN</span>
                                        <div class="font-medium"><?php echo htmlspecialchars($person['lrn'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                    <div>
                                        <span class="text-muted text-sm">Grade Level</span>
                                        <div class="font-medium"><?php echo htmlspecialchars($person['grade_level'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                    <div>
                                        <span class="text-muted text-sm">Section</span>
                                        <div class="font-medium"><?php echo htmlspecialchars($section ?: 'Not Assigned', ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                    <?php if($strand): ?>
                                    <div>
                                        <span class="text-muted text-sm">Strand</span>
                                        <div class="font-medium"><?php echo htmlspecialchars($strand, ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="student_profile.php" class="btn btn-secondary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                View Profile
                            </a>
                            <a href="student_grades.php" class="btn btn-secondary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                View Grades
                            </a>
                            <a href="student_attendance.php" class="btn btn-secondary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                Attendance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid-stats">
                <div class="card stat-card">
                    <div class="stat-card-icon primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $subjects_count; ?></div>
                    <div class="stat-card-label">Enrolled Subjects</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon <?php echo $gwa >= 75 ? 'success' : 'danger'; ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $gwa > 0 ? $gwa : 'N/A'; ?></div>
                    <div class="stat-card-label">General Weighted Average</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon <?php echo $attendance_percentage >= 80 ? 'success' : ($attendance_percentage >= 60 ? 'warning' : 'danger'); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $attendance_percentage; ?>%</div>
                    <div class="stat-card-label">Attendance Rate</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon accent">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $attendance_total; ?></div>
                    <div class="stat-card-label">Total School Days</div>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Attendance Summary</h3>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom: var(--spacing-4);">
                            <div class="d-flex" style="justify-content: space-between; margin-bottom: var(--spacing-2);">
                                <span class="text-sm text-muted">Attendance Progress</span>
                                <span class="text-sm font-medium"><?php echo $attendance_percentage; ?>%</span>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar" style="width: <?php echo $attendance_percentage; ?>%; background: <?php echo $attendance_percentage >= 80 ? 'var(--color-success)' : ($attendance_percentage >= 60 ? 'var(--color-warning)' : 'var(--color-danger)'); ?>;"></div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-4" style="flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 100px; text-align: center; padding: var(--spacing-3); background: var(--color-success-light); border-radius: var(--radius-lg);">
                                <div style="font-size: var(--font-size-2xl); font-weight: var(--font-weight-bold); color: var(--color-success);"><?php echo $attendance_present; ?></div>
                                <div class="text-sm text-muted">Present</div>
                            </div>
                            <div style="flex: 1; min-width: 100px; text-align: center; padding: var(--spacing-3); background: var(--color-danger-light); border-radius: var(--radius-lg);">
                                <div style="font-size: var(--font-size-2xl); font-weight: var(--font-weight-bold); color: var(--color-danger);"><?php echo $attendance_absent; ?></div>
                                <div class="text-sm text-muted">Absent</div>
                            </div>
                            <div style="flex: 1; min-width: 100px; text-align: center; padding: var(--spacing-3); background: var(--color-warning-light); border-radius: var(--radius-lg);">
                                <div style="font-size: var(--font-size-2xl); font-weight: var(--font-weight-bold); color: var(--color-warning);"><?php echo $attendance_late; ?></div>
                                <div class="text-sm text-muted">Late</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Announcements</h3>
                    </div>
                    <div class="card-body p-0">
                        <div style="max-height: 280px; overflow-y: auto;">
                            <?php foreach($announcements as $announcement): ?>
                            <div style="padding: var(--spacing-4) var(--spacing-5); border-bottom: 1px solid var(--color-border-light); display: flex; align-items: center; gap: var(--spacing-4);">
                                <div class="stat-card-icon <?php echo $announcement['type']; ?>" style="width: 40px; height: 40px; margin-bottom: 0; flex-shrink: 0;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                    </svg>
                                </div>
                                <div style="flex: 1;">
                                    <div class="font-medium"><?php echo htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="text-sm text-muted"><?php echo htmlspecialchars($announcement['date'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>
