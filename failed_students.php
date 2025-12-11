<?php
// failed_students.php

include "session.php";
require_login();
if($_SESSION['role'] !== 'admin') { 
    header('Location: index.php'); 
    exit; 
}

$page_title = 'Failed Students Report';
$db_available = false;
$pdo = null;

try {
    // 1. Database Connection Check
    include "dbconfig.php";
    $db_available = isset($pdo) && $pdo !== null;
} catch(Exception $e) {
    $db_available = false;
}

$failed_students = []; 
$failing_grade_threshold = 75.00; 

if($db_available) {
    try {
        // 2. Enhanced SQL Query: Includes student's grade_level
        $stmt = $pdo->prepare("
            SELECT
                s.lrn,
                s.family_name,
                s.first_name,
                s.grade_level,         
                sub.code AS subject_code,
                sub.name AS subject_name,
                g.grade AS final_grade,
                e.school_year,
                e.semester
            FROM
                students s
            JOIN
                enrollments e ON s.id = e.student_id
            JOIN
                grades g ON e.id = g.enrollment_id
            JOIN
                subject_loads sl ON e.subject_load_id = sl.id
            JOIN
                subjects sub ON sl.subject_id = sub.id
            WHERE
                g.grade < :threshold
            ORDER BY
                s.grade_level, s.family_name, s.first_name, e.school_year, e.semester
        ");
        $stmt->bindParam(':threshold', $failing_grade_threshold, PDO::PARAM_STR);
        $stmt->execute();
        $failed_students = $stmt->fetchAll();

    } catch(Exception $e) {
        // Log database error here in production
    }
} else {
    // 3. Fallback/Dummy Data
    $failed_students = [
        ['lrn' => 'LRN0000013', 'family_name' => 'Villar', 'first_name' => 'Ben', 'grade_level' => 'Grade 11', 'subject_code' => 'HUMSS11-ENG', 'subject_name' => 'HUMSS English', 'final_grade' => '69.00', 'school_year' => '2024-2025', 'semester' => 'Second'],
        ['lrn' => 'LRN0000014', 'family_name' => 'Panganiban', 'first_name' => 'Tess', 'grade_level' => 'Grade 9', 'subject_code' => 'ENG9', 'subject_name' => 'English 9', 'final_grade' => '69.50', 'school_year' => '2024-2025', 'semester' => 'First'],
        ['lrn' => 'LRN0000008', 'family_name' => 'Velasco', 'first_name' => 'Rosa', 'grade_level' => 'Grade 9', 'subject_code' => 'HIST8', 'subject_name' => 'History 8', 'final_grade' => '72.75', 'school_year' => '2024-2025', 'semester' => 'First'],
        ['lrn' => 'LRN0000011', 'family_name' => 'Sanchez', 'first_name' => 'Ivy', 'grade_level' => 'Grade 11', 'subject_code' => 'HUMSS11-ENG', 'subject_name' => 'HUMSS English', 'final_grade' => '73.50', 'school_year' => '2024-2025', 'semester' => 'First'],
        ['lrn' => 'LRN0000016', 'family_name' => 'Castro', 'first_name' => 'Bea', 'grade_level' => 'Grade 8', 'subject_code' => 'MATH8', 'subject_name' => 'Mathematics 8', 'final_grade' => '74.25', 'school_year' => '2024-2025', 'semester' => 'Second']
    ];
}

$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'admin_dashboard.php'],
    ['title' => 'Failed Students', 'active' => true]
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
                        <h1 class="page-header-title">Failed Students Report 🚨</h1>
                        <p class="page-header-subtitle">List of all recorded failing grades (Final Grade &lt; <?php echo number_format($failing_grade_threshold, 2); ?>) across all subjects.</p>
                    </div>
                    <div class="page-header-actions">
                        <button class="btn btn-secondary" onclick="window.print()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                <rect x="6" y="14" width="12" height="8"></rect>
                            </svg>
                            Print Report
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Failing Grades Breakdown (Total: <?php echo count($failed_students); ?> entries)</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 10%">LRN</th>
                                    <th style="width: 20%">Student Name</th>
                                    <th style="width: 15%">Grade Level</th>
                                    <th style="width: 30%">Subject Failed</th>
                                    <th style="width: 10%" class="text-right">Final Grade</th>
                                    <th style="width: 15%">School Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($failed_students)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-6">🎉 No failing grades found. All students are performing above the minimum standard of <?php echo number_format($failing_grade_threshold, 2); ?>.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($failed_students as $fs): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fs['lrn'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span class="font-medium">
                                            <?php echo htmlspecialchars($fs['family_name'] . ', ' . $fs['first_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary"><?php echo htmlspecialchars($fs['grade_level'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td>
                                        <span class="font-medium"><?php echo htmlspecialchars($fs['subject_name'] . ' (' . $fs['subject_code'] . ')', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-danger grade-badge"><?php echo number_format((float)$fs['final_grade'], 2); ?></span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($fs['school_year'] . ' / ' . $fs['semester'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-sm text-muted">
                    Displaying results for all years where a final grade is recorded below <?php echo number_format($failing_grade_threshold, 2); ?>.
                </div>
            </div>

            <div class="mt-6">
                 <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>

        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>