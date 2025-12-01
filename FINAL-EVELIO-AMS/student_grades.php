<?php
include "session.php";
require_login();
if($_SESSION['role']!=='student'){ header('Location: index.php'); exit; }

$page_title = 'My Grades';
$db_available = false;
$crud = null;

try {
    include "pdo_functions.php";
    $crud = new pdoCRUD();
    $db_available = true;
} catch(Exception $e) {
    $db_available = false;
}

$sid = 0;
$person = null;
$sy = $_GET['sy'] ?? '';
$sem = $_GET['sem'] ?? '';
$enrollments = [];
$gwa = 0;
$total_units = 0;
$passed_count = 0;
$failed_count = 0;
$available_years = [];

if($db_available && $crud) {
    try {
        $acc = $crud->getAccountById($_SESSION['account_id']);
        $sid = $acc['person_id'];
        $person = $crud->getAccountPerson('student', $sid);
        
        include "dbconfig.php";
        
        if($sy){
            $all = $crud->getEnrollmentsByStudent($sid);
            foreach($all as $e){ 
                if($e['school_year']===$sy && ($sem==='' || ($e['semester']??'')===$sem)) {
                    $gs = $pdo->prepare('SELECT grade FROM grades WHERE enrollment_id = :id');
                    $gs->execute([':id'=>$e['id']]);
                    $g = $gs->fetch();
                    $e['grade'] = $g['grade'] ?? null;
                    $enrollments[] = $e;
                    
                    if($e['grade'] !== null) {
                        $total_units++;
                        if($e['grade'] >= 75) {
                            $passed_count++;
                        } else {
                            $failed_count++;
                        }
                    }
                }
            }
            
            if($total_units > 0) {
                $gradeSum = 0;
                foreach($enrollments as $e) {
                    if($e['grade'] !== null) {
                        $gradeSum += $e['grade'];
                    }
                }
                $gwa = round($gradeSum / $total_units, 2);
            }
        }
        
        $yearsStmt = $pdo->prepare("SELECT DISTINCT school_year FROM enrollments WHERE student_id = :sid ORDER BY school_year DESC");
        $yearsStmt->execute([':sid'=>$sid]);
        $available_years = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(Exception $e) {
        $db_available = false;
    }
}

if(!$db_available) {
    $available_years = ['2024-2025', '2023-2024'];
    if(!$sy) $sy = '2024-2025';
    
    $enrollments = [
        ['id' => 1, 'subject_name' => 'Mathematics 11', 'section_name' => 'Einstein', 'school_year' => '2024-2025', 'semester' => 'First', 'grade' => 92],
        ['id' => 2, 'subject_name' => 'English 11', 'section_name' => 'Einstein', 'school_year' => '2024-2025', 'semester' => 'First', 'grade' => 88],
        ['id' => 3, 'subject_name' => 'Science 11', 'section_name' => 'Einstein', 'school_year' => '2024-2025', 'semester' => 'First', 'grade' => 90],
        ['id' => 4, 'subject_name' => 'Filipino 11', 'section_name' => 'Einstein', 'school_year' => '2024-2025', 'semester' => 'First', 'grade' => 85],
        ['id' => 5, 'subject_name' => 'PE 11', 'section_name' => 'Einstein', 'school_year' => '2024-2025', 'semester' => 'First', 'grade' => null]
    ];
    
    $total_units = 4;
    $passed_count = 4;
    $failed_count = 0;
    $gwa = 88.75;
}

$breadcrumb = [
    ['title' => 'My Grades', 'active' => true]
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
                        <h1 class="page-header-title">My Grades</h1>
                        <p class="page-header-subtitle">View your academic performance and grades.</p>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-bottom: var(--spacing-6);">
                <div class="card-header">
                    <h3 class="card-title">Filter Grades</h3>
                </div>
                <div class="card-body">
                    <form method="get" class="d-flex gap-4 flex-wrap" style="align-items: flex-end;">
                        <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                            <label class="form-label">School Year</label>
                            <select name="sy" class="form-control" required>
                                <option value="">Select School Year</option>
                                <?php foreach($available_years as $year): ?>
                                <option value="<?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $sy === $year ? 'selected' : ''; ?>><?php echo htmlspecialchars($year, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                                <?php if(empty($available_years) || !in_array($sy, $available_years)): ?>
                                <option value="<?php echo htmlspecialchars($sy ?: date('Y').'-'.(date('Y')+1), ENT_QUOTES, 'UTF-8'); ?>" <?php echo $sy ? 'selected' : ''; ?>><?php echo htmlspecialchars($sy ?: date('Y').'-'.(date('Y')+1), ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                            <label class="form-label">Semester (SHS)</label>
                            <select name="sem" class="form-control">
                                <option value="">All</option>
                                <option value="First" <?php echo $sem==='First'?'selected':''; ?>>First Semester</option>
                                <option value="Second" <?php echo $sem==='Second'?'selected':''; ?>>Second Semester</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            Load Grades
                        </button>
                    </form>
                </div>
            </div>
            
            <?php if($sy): ?>
            <div class="grid-stats" style="margin-bottom: var(--spacing-6);">
                <div class="card stat-card">
                    <div class="stat-card-icon <?php echo $gwa >= 75 ? 'success' : ($gwa > 0 ? 'danger' : 'primary'); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $gwa > 0 ? $gwa : 'N/A'; ?></div>
                    <div class="stat-card-label">General Weighted Average</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon primary">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo count($enrollments); ?></div>
                    <div class="stat-card-label">Total Subjects</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon success">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $passed_count; ?></div>
                    <div class="stat-card-label">Subjects Passed</div>
                </div>
                
                <div class="card stat-card">
                    <div class="stat-card-icon danger">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </div>
                    <div class="stat-card-value"><?php echo $failed_count; ?></div>
                    <div class="stat-card-label">Subjects Failed</div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Grades for <?php echo htmlspecialchars($sy, ENT_QUOTES, 'UTF-8'); ?> <?php echo $sem ? '- '.htmlspecialchars($sem, ENT_QUOTES, 'UTF-8').' Semester' : ''; ?></h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-container" style="border: none; box-shadow: none;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Section</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($enrollments)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted" style="padding: var(--spacing-8);">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto var(--spacing-3); display: block; opacity: 0.5;">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        No grades found for this period
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($enrollments as $e): ?>
                                <tr>
                                    <td><span class="font-medium"><?php echo htmlspecialchars($e['subject_name'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td><?php echo htmlspecialchars($e['section_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php if($e['grade'] !== null): ?>
                                        <span class="font-medium" style="font-size: var(--font-size-lg);"><?php echo htmlspecialchars($e['grade'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($e['grade'] !== null): ?>
                                            <?php if($e['grade'] >= 75): ?>
                                            <span class="badge badge-success">Passed</span>
                                            <?php else: ?>
                                            <span class="badge badge-danger">Failed</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                        <span class="badge badge-secondary">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if(!empty($enrollments)): ?>
                <div class="card-footer">
                    <div class="d-flex" style="justify-content: space-between; align-items: center;">
                        <span class="text-muted text-sm">Showing <?php echo count($enrollments); ?> subject(s)</span>
                        <div class="d-flex gap-3">
                            <span class="badge badge-success"><?php echo $passed_count; ?> Passed</span>
                            <span class="badge badge-danger"><?php echo $failed_count; ?> Failed</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="card-body" style="text-align: center; padding: var(--spacing-12);">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto var(--spacing-4); display: block; color: var(--color-text-muted);">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    <h3 style="margin-bottom: var(--spacing-2);">Select a School Year</h3>
                    <p class="text-muted">Choose a school year from the filter above to view your grades.</p>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>
