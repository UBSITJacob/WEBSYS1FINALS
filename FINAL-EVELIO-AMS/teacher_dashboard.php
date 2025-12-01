<?php
include "session.php";
require_login();
if($_SESSION['role']!=='teacher'){ header('Location: index.php'); exit; }

$db_available = false;
$pdoC = null;

try {
    include "pdo_functions.php";
    $pdoC = new pdoCRUD();
    $db_available = true;
} catch(Exception $e) {
    $db_available = false;
}

$acc = null;
$person = null;
$loads = [];
$total_loads = 0;
$section_id = 0;
$advisory_section = null;
$advisory_students_count = 0;

if($db_available && $pdoC) {
    try {
        $acc = $pdoC->getAccountById($_SESSION['account_id']);
        $person = $pdoC->getAccountPerson('teacher',$acc['person_id']);
        $loads = $pdoC->getTeacherLoads($acc['person_id']);
        $total_loads = count($loads);
        $section_id = (int)($person['advisory_section_id'] ?? 0);
        if($section_id){
            $advisory_students_count = $pdoC->countAdvisoryStudentsForTeacher($acc['person_id'], '');
        }
    } catch(Exception $e) {
        $db_available = false;
    }
}

if(!$db_available) {
    $person = [
        'id' => 1,
        'faculty_id' => 'FAC-2024-001',
        'first_name' => 'Maria',
        'family_name' => 'Santos',
        'full_name' => 'Maria Santos',
        'sex' => 'Female',
        'active' => 1,
        'advisory_section_id' => 1,
        'section_name' => 'Einstein'
    ];
    $loads = [
        ['id' => 1, 'subject_code' => 'MATH101', 'subject_name' => 'Mathematics 7', 'section_name' => 'Einstein'],
        ['id' => 2, 'subject_code' => 'MATH102', 'subject_name' => 'Mathematics 8', 'section_name' => 'Newton'],
        ['id' => 3, 'subject_code' => 'MATH201', 'subject_name' => 'Pre-Calculus', 'section_name' => 'Galileo']
    ];
    $total_loads = count($loads);
    $section_id = 1;
    $advisory_students_count = 32;
}

$page_title = 'Teacher Dashboard';
$breadcrumb = [
    ['title' => 'Dashboard', 'active' => true]
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
                        <h1 class="page-header-title">Welcome, <?php echo htmlspecialchars($person['full_name'] ?? 'Teacher', ENT_QUOTES, 'UTF-8'); ?>!</h1>
                        <p class="page-header-subtitle">Here's your teaching overview for today.</p>
                    </div>
                    <div class="page-header-actions">
                        <a href="attendance.php" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                            Take Attendance
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="grid-2 mb-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Teacher Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-center gap-4 mb-4">
                            <div class="avatar avatar-xl avatar-primary">
                                <?php echo strtoupper(substr($person['first_name'] ?? 'T', 0, 1) . substr($person['family_name'] ?? 'C', 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-1"><?php echo htmlspecialchars($person['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h4>
                                <p class="text-muted text-sm mb-0">Faculty Member</p>
                            </div>
                        </div>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    Faculty ID
                                </span>
                                <span class="info-value"><?php echo htmlspecialchars($person['faculty_id'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    Email
                                </span>
                                <span class="info-value"><?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    Status
                                </span>
                                <span class="info-value">
                                    <?php if($person['active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Advisory Section</h3>
                    </div>
                    <div class="card-body">
                        <?php if($section_id): ?>
                            <div class="d-flex align-center gap-4 mb-4">
                                <div class="stat-card-icon accent">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold mb-1"><?php echo htmlspecialchars($person['section_name'] ?? 'Section', ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <p class="text-muted text-sm mb-0"><?php echo (int)$advisory_students_count; ?> Students</p>
                                </div>
                            </div>
                            <a href="advisory_class.php" class="btn btn-secondary btn-block">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                View Advisory Class
                            </a>
                        <?php else: ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <line x1="23" y1="11" x2="17" y2="11"></line>
                                    </svg>
                                </div>
                                <p class="text-muted">No advisory section assigned</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="grid-stats">
                <div class="card stat-card">
                    <div class="stat-card-icon primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $total_loads; ?></div>
                    <div class="stat-card-label">Subject Loads</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon accent">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo (int)$advisory_students_count; ?></div>
                    <div class="stat-card-label">Advisory Students</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo date('F'); ?></div>
                    <div class="stat-card-label">Current Month</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon warning">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo date('Y'); ?></div>
                    <div class="stat-card-label">School Year</div>
                </div>
            </div>
            
            <?php if($total_loads > 0): ?>
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="card-title">My Subject Loads</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Section</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($loads as $load): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <span class="badge badge-primary"><?php echo htmlspecialchars($load['subject_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td>
                                        <span class="font-medium"><?php echo htmlspecialchars($load['subject_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($load['section_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="grades.php?load_id=<?php echo (int)$load['id']; ?>" class="btn btn-sm btn-secondary">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                                </svg>
                                                Grades
                                            </a>
                                            <a href="attendance.php?load_id=<?php echo (int)$load['id']; ?>" class="btn btn-sm btn-secondary">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <polyline points="16 11 18 13 22 9"></polyline>
                                                </svg>
                                                Attendance
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="advisory_class.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Advisory Class
                        </a>
                        <a href="attendance.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                            Mark Attendance
                        </a>
                        <a href="grades.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            Manage Grades
                        </a>
                        <a href="change_password.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Change Password
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.mb-6 { margin-bottom: var(--spacing-6); }
.mb-4 { margin-bottom: var(--spacing-4); }
.mb-1 { margin-bottom: var(--spacing-1); }
.mb-0 { margin-bottom: 0; }
.gap-4 { gap: var(--spacing-4); }
.gap-3 { gap: var(--spacing-3); }
.font-semibold { font-weight: var(--font-weight-semibold); }
.text-muted { color: var(--color-text-muted); }
.text-sm { font-size: var(--font-size-sm); }
.d-flex { display: flex; }
.align-center { align-items: center; }
.flex-wrap { flex-wrap: wrap; }

.info-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-3);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-3);
    background-color: var(--color-gray-50);
    border-radius: var(--radius-lg);
}

.info-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-2);
    color: var(--color-text-secondary);
    font-size: var(--font-size-sm);
}

.info-value {
    font-weight: var(--font-weight-medium);
    color: var(--color-text-primary);
}

.empty-state {
    text-align: center;
    padding: var(--spacing-8);
}

.empty-state-icon {
    color: var(--color-text-muted);
    margin-bottom: var(--spacing-4);
}

.btn-block {
    width: 100%;
}
</style>

<?php include "includes/footer.php"; ?>
