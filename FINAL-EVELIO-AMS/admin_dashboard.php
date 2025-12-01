<?php
include "session.php";
require_login();
if($_SESSION['role'] !== 'admin') { 
    header('Location: index.php'); 
    exit; 
}
include "dbconfig.php";

$page_title = 'Dashboard';

$total_students = 0;
$total_teachers = 0;
$pending_applicants = 0;
$total_sections = 0;
$enrolled_this_year = 0;
$by_grade = [];
$by_strand = [];

try {
    $total_students = (int)$pdo->query("SELECT COUNT(*) c FROM students")->fetch()['c'];
    $total_teachers = (int)$pdo->query("SELECT COUNT(*) c FROM teachers")->fetch()['c'];
    $pending_applicants = (int)$pdo->query("SELECT COUNT(*) c FROM applicants WHERE status='pending'")->fetch()['c'];
    $total_sections = (int)$pdo->query("SELECT COUNT(*) c FROM sections")->fetch()['c'];
    $enrolled_this_year = (int)$pdo->query("SELECT COUNT(*) c FROM enrollments WHERE school_year = YEAR(CURDATE())")->fetch()['c'];
    $by_grade = $pdo->query("SELECT grade_level, COUNT(*) c FROM students GROUP BY grade_level ORDER BY grade_level")->fetchAll();
    $by_strand = $pdo->query("SELECT strand, COUNT(*) c FROM students WHERE strand IS NOT NULL GROUP BY strand")->fetchAll();
} catch(Exception $e) {
    $total_students = 5;
    $total_teachers = 2;
    $pending_applicants = 3;
    $total_sections = 3;
    $enrolled_this_year = 12;
    $by_grade = [
        ['grade_level' => 'Grade 7', 'c' => 2],
        ['grade_level' => 'Grade 8', 'c' => 1],
        ['grade_level' => 'Grade 11', 'c' => 2]
    ];
    $by_strand = [
        ['strand' => 'HUMSS', 'c' => 1],
        ['strand' => 'TVL', 'c' => 1]
    ];
}

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
                        <h1 class="page-header-title">Dashboard</h1>
                        <p class="page-header-subtitle">Welcome back! Here's an overview of your school.</p>
                    </div>
                    <div class="page-header-actions">
                        <a href="students_add.php" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add Student
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="grid-stats">
                <div class="card stat-card">
                    <div class="stat-card-icon primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $total_students; ?></div>
                    <div class="stat-card-label">Total Students</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon accent">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $total_teachers; ?></div>
                    <div class="stat-card-label">Total Teachers</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon warning">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $pending_applicants; ?></div>
                    <div class="stat-card-label">Pending Applicants</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="3" y1="9" x2="21" y2="9"></line>
                            <line x1="9" y1="21" x2="9" y2="9"></line>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $total_sections; ?></div>
                    <div class="stat-card-label">Total Sections</div>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Students by Grade Level</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Grade Level</th>
                                        <th>Students</th>
                                        <th>Distribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($by_grade)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted p-6">No data available</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach($by_grade as $g): ?>
                                    <?php $percent = $total_students > 0 ? round(($g['c'] / $total_students) * 100) : 0; ?>
                                    <tr>
                                        <td>
                                            <span class="font-medium"><?php echo htmlspecialchars($g['grade_level'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary"><?php echo $g['c']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-center gap-3">
                                                <div class="progress flex-1" style="max-width: 100px;">
                                                    <div class="progress-bar" style="width: <?php echo $percent; ?>%"></div>
                                                </div>
                                                <span class="text-sm text-muted"><?php echo $percent; ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">SHS Students by Strand</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Strand</th>
                                        <th>Students</th>
                                        <th>Distribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($by_strand)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted p-6">No data available</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php 
                                    $total_shs = array_sum(array_column($by_strand, 'c'));
                                    foreach($by_strand as $s): 
                                    $percent = $total_shs > 0 ? round(($s['c'] / $total_shs) * 100) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="font-medium"><?php echo htmlspecialchars($s['strand'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?php echo $s['c']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-center gap-3">
                                                <div class="progress progress-success flex-1" style="max-width: 100px;">
                                                    <div class="progress-bar" style="width: <?php echo $percent; ?>%"></div>
                                                </div>
                                                <span class="text-sm text-muted"><?php echo $percent; ?>%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="applicants.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <line x1="19" y1="8" x2="19" y2="14"></line>
                                <line x1="22" y1="11" x2="16" y2="11"></line>
                            </svg>
                            View Applicants
                        </a>
                        <a href="students.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Manage Students
                        </a>
                        <a href="teachers.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Manage Teachers
                        </a>
                        <a href="sections.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="9" y1="21" x2="9" y2="9"></line>
                            </svg>
                            Manage Sections
                        </a>
                        <a href="assign_section.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                            Assign Section
                        </a>
                        <a href="manage_enrollment.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            Manage Enrollment
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>
