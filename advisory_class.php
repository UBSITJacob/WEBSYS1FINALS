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

$q = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'family_name';
$dir = $_GET['dir'] ?? 'ASC';
$page = max(1,(int)($_GET['page'] ?? 1));
$limit = max(1,min(50,(int)($_GET['limit'] ?? 10)));

$rows = [];
$total = 0;
$totalPages = 1;
$section_id = 0;
$section_name = 'Advisory Class';
$teacher = null;

if($db_available && $crud) {
    try {
        $acc = $crud->getAccountById($_SESSION['account_id']);
        $teacher = $crud->getAccountPerson('teacher',$acc['person_id']);
        $rows = $crud->getAdvisoryStudentsForTeacher($acc['person_id'],$q,$page,$limit,$sort,$dir);
        $total = $crud->countAdvisoryStudentsForTeacher($acc['person_id'],$q);
        $totalPages = max(1, ceil($total / $limit));
        $section_id = (int)($teacher['advisory_section_id'] ?? 0);
        $section_name = $teacher['section_name'] ?? 'Advisory Class';
    } catch(Exception $e) {
        $db_available = false;
    }
}

if(!$db_available) {
    $section_name = 'Einstein (Demo)';
    $rows = [
        ['id' => 1, 'lrn' => '123456789001', 'first_name' => 'Juan', 'family_name' => 'Dela Cruz', 'sex' => 'Male', 'grade_level' => 'Grade 11'],
        ['id' => 2, 'lrn' => '123456789002', 'first_name' => 'Maria', 'family_name' => 'Santos', 'sex' => 'Female', 'grade_level' => 'Grade 11'],
        ['id' => 3, 'lrn' => '123456789003', 'first_name' => 'Pedro', 'family_name' => 'Reyes', 'sex' => 'Male', 'grade_level' => 'Grade 11'],
        ['id' => 4, 'lrn' => '123456789004', 'first_name' => 'Ana', 'family_name' => 'Garcia', 'sex' => 'Female', 'grade_level' => 'Grade 11'],
        ['id' => 5, 'lrn' => '123456789005', 'first_name' => 'Jose', 'family_name' => 'Rizal', 'sex' => 'Male', 'grade_level' => 'Grade 11']
    ];
    $total = count($rows);
    $totalPages = 1;
}

$page_title = 'Advisory Class';
$breadcrumb = [
    ['title' => 'Dashboard', 'url' => 'teacher_dashboard.php'],
    ['title' => 'Advisory Class', 'active' => true]
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
                        <h1 class="page-header-title"><?php echo htmlspecialchars($section_name, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <p class="page-header-subtitle">Advisory Class - <?php echo (int)$total; ?> Students</p>
                    </div>
                    <div class="page-header-actions">
                        <button onclick="exportToPDF()" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            Export PDF
                        </button>
                        <button onclick="window.print()" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                <rect x="6" y="14" width="12" height="8"></rect>
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-between align-center flex-wrap gap-4">
                        <h3 class="card-title">Student List</h3>
                        <div class="search-filter-bar" style="margin-bottom: 0;">
                            <div class="search-input-wrapper">
                                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" class="form-control" placeholder="Search by name or LRN..." value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" onkeyup="handleSearch(this.value)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table" id="studentTable">
                            <thead>
                                <tr>
                                    
                                    <th onclick="setSort('family_name')" class="sortable">
                                        Name
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sort-icon">
                                            <path d="M7 15l5 5 5-5M7 9l5-5 5 5"/>
                                        </svg>
                                    </th>
                                    <th onclick="setSort('sex')" class="sortable">
                                        Sex
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sort-icon">
                                            <path d="M7 15l5 5 5-5M7 9l5-5 5 5"/>
                                        </svg>
                                    </th>
                                    <th onclick="setSort('grade_level')" class="sortable">
                                        Grade Level
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sort-icon">
                                            <path d="M7 15l5 5 5-5M7 9l5-5 5 5"/>
                                        </svg>
                                    </th>
                                    <th>Status</th>
                                    <th>Actions</th>

                                  
    
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!$rows): ?>
                                <tr>
                                    <td colspan="6" class="text-center p-6">
                                        <div class="empty-state-inline">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-muted mb-3">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <line x1="23" y1="11" x2="17" y2="11"></line>
                                            </svg>
                                            <p class="text-muted mb-0">No students found in your advisory class</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($rows as $r): ?>
                                <tr>
                                    
                                    <td>
                                        <div class="d-flex align-center gap-3">
                                            <div class="avatar avatar-sm avatar-primary">
                                                <?php echo strtoupper(substr($r['first_name'] ?? 'S', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <span class="font-medium"><?php echo htmlspecialchars($r['family_name'] . ', ' . $r['first_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo ($r['sex'] ?? '') === 'Male' ? 'badge-info' : 'badge-secondary'; ?>">
                                            <?php echo htmlspecialchars($r['sex'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($r['grade_level'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span class="badge badge-success">Enrolled</span>
                                    </td>
                                    
                                    <td>
                                        <div class="table-actions">
                                            <button onclick="viewStudent(<?php echo (int)$r['id']; ?>, '<?php echo htmlspecialchars($r['family_name'] . ', ' . $r['first_name'], ENT_QUOTES, 'UTF-8'); ?>')" class="btn btn-sm btn-secondary">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if($total > 0): ?>
                <div class="card-footer">
                    <div class="d-flex justify-between align-center flex-wrap gap-3">
                        <p class="text-sm text-muted mb-0">
                            Showing <?php echo (($page - 1) * $limit) + 1; ?> to <?php echo min($page * $limit, $total); ?> of <?php echo $total; ?> students
                        </p>
                        <div class="pagination">
                            <button onclick="goToPage(<?php echo $page - 1; ?>)" class="pagination-item <?php echo $page <= 1 ? 'disabled' : ''; ?>" <?php echo $page <= 1 ? 'disabled' : ''; ?>>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </button>
                            <?php for($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <span class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></span>
                            <?php endfor; ?>
                            <button onclick="goToPage(<?php echo $page + 1; ?>)" class="pagination-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>" <?php echo $page >= $totalPages ? 'disabled' : ''; ?>>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<div class="modal-backdrop" id="modalBackdrop" onclick="closeStudentModal()"></div>
<div class="modal modal-xl" id="studentModal" style="width: 92vw; max-width: 1200px;">
    <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Student Details</h5>
        <button type="button" class="modal-close" id="closeModalBtn" onclick="closeStudentModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    <div class="modal-body">
        <div id="modalBody"></div>
    </div>
    <div class="modal-footer">
        </div>
</div>

<style>
.sortable {
    cursor: pointer;
    user-select: none;
}
.sortable:hover {
    background-color: var(--color-gray-100);
}
.sort-icon {
    opacity: 0.5;
    margin-left: var(--spacing-1);
    vertical-align: middle;
}
.d-flex { display: flex; }
.justify-between { justify-content: space-between; }
.align-center { align-items: center; }
.flex-wrap { flex-wrap: wrap; }
.gap-4 { gap: var(--spacing-4); }
.gap-3 { gap: var(--spacing-3); }
.p-6 { padding: var(--spacing-6); }
.p-0 { padding: 0; }
.mb-3 { margin-bottom: var(--spacing-3); }
.mb-0 { margin-bottom: 0; }
.text-center { text-align: center; }
.text-muted { color: var(--color-text-muted); }
.text-sm { font-size: var(--font-size-sm); }
.font-medium { font-weight: var(--font-weight-medium); }
.empty-state-inline {
    padding: var(--spacing-8);
}

/* --- New Attendance Summary Styles (used by getStudentReport.php output) --- */
.attendance-summary {
    display: flex;
    gap: var(--spacing-4);
    margin-top: var(--spacing-4);
    margin-bottom: var(--spacing-6);
    flex-wrap: wrap;
}
.stat-card {
    background-color: var(--color-gray-50);
    padding: var(--spacing-4);
    border-radius: var(--border-radius-lg);
    border: 1px solid var(--color-gray-200);
    min-width: 150px;
    text-align: center;
}
.stat-label {
    display: block;
    font-size: var(--font-size-sm);
    color: var(--color-text-muted);
    margin-bottom: var(--spacing-1);
}
.stat-value {
    display: block;
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-bold);
}
.text-success { color: #10b981; /* Example success color */ }
.text-warning { color: #f59e0b; /* Example warning color */ }
.text-danger { color: #ef4444; /* Example danger color */ }


@media print {
    /* Hide navigational elements for main print */
    .sidebar, .top-header, .page-header-actions, .search-filter-bar, .pagination, .table-actions, .card-footer {
        display: none !important;
    }
    .main-wrapper {
        margin-left: 0 !important;
    }
    .card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    /* Ensure summary cards print clearly */
    .attendance-summary { display: flex; justify-content: space-around; }
    .stat-card {
        display: inline-block;
        margin-right: 15px;
        background-color: #fff;
        border: 1px solid #ccc;
        flex: none; /* Override flex properties for print layout safety */
    }
}
</style>

<script>
let currentPage = <?php echo (int)$page; ?>;
let currentLimit = <?php echo (int)$limit; ?>;
let currentQ = "<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>";
let currentSort = "<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>";
let currentDir = "<?php echo htmlspecialchars($dir, ENT_QUOTES, 'UTF-8'); ?>";

let searchTimeout;
function handleSearch(value) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentQ = value.trim();
        currentPage = 1;
        reload();
    }, 300);
}

function setSort(col) {
    if (currentSort === col) {
        currentDir = currentDir === 'ASC' ? 'DESC' : 'ASC';
    } else {
        currentSort = col;
        currentDir = 'ASC';
    }
    reload();
}

function goToPage(p) {
    if (p < 1) return;
    currentPage = p;
    reload();
}

function reload() {
    const params = new URLSearchParams();
    params.set('q', currentQ);
    params.set('sort', currentSort);
    params.set('dir', currentDir);
    params.set('page', currentPage);
    params.set('limit', currentLimit);
    window.location.href = 'advisory_class.php?' + params.toString();
}

/**
 * Handles viewing the student details and dynamically adds the print button.
 */
function viewStudent(id, name) {
    document.getElementById('modalTitle').textContent = name;
    document.getElementById('modalBody').innerHTML = '<div class="text-muted">Loading...</div>';
    
    // Get the modal footer element
    const modalFooter = document.querySelector('#studentModal .modal-footer');
    // Set a default/loading close button
    modalFooter.innerHTML = '<button class="btn btn-secondary" onclick="closeStudentModal()">Close</button>';
    
    document.getElementById('modalBackdrop').classList.add('active');
    document.getElementById('studentModal').classList.add('active');
    
    const year = new Date().getFullYear();
    fetch('getStudentReport.php?student_id='+id+'&year='+year)
        .then(r=>r.text())
        .then(html=>{ 
            document.getElementById('modalBody').innerHTML = html;
            
            // Add the new Print button and Close button to the modal footer
            modalFooter.innerHTML = `
                <button class="btn btn-secondary" onclick="closeStudentModal()">Close</button>
                <button class="btn btn-primary" onclick="printStudentReport('${name}')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print Report
                </button>
            `;
        })
        .catch(()=>{ 
            document.getElementById('modalBody').innerHTML = '<div class="text-danger">Failed to load report</div>'; 
            modalFooter.innerHTML = '<button class="btn btn-secondary" onclick="closeStudentModal()">Close</button>';
        });
}

/**
 * Isolates and prints the content inside the student modal.
 */
function printStudentReport(studentName) {
    const content = document.getElementById('modalBody').innerHTML;
    const printWindow = window.open('', '', 'height=600,width=800');
    
    printWindow.document.write('<html><head><title>Student Report: ' + studentName + '</title>');
    
    // Include minimal print styles to ensure readability
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; font-size: 11pt; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-bottom: 20px; page-break-inside: auto; }');
    printWindow.document.write('tr { page-break-inside: avoid; page-break-after: auto; }');
    printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
    printWindow.document.write('th { background-color: #f2f2f2; }');
    printWindow.document.write('h1, h2, h3, h4 { color: #333; margin-top: 20px; margin-bottom: 10px; }');
    
    // Print styles for the new attendance summary
    printWindow.document.write('.attendance-summary { display: flex; justify-content: space-around; gap: 20px; margin-bottom: 20px; }');
    printWindow.document.write('.stat-card { border: 1px solid #ccc; padding: 10px; border-radius: 5px; text-align: center; flex: 1; }');
    printWindow.document.write('.stat-label { font-size: 9pt; color: #666; display: block; }');
    printWindow.document.write('.stat-value { font-size: 14pt; font-weight: bold; display: block; }');
    printWindow.document.write('.text-success { color: #10b981; }');
    printWindow.document.write('.text-warning { color: #f59e0b; }');
    printWindow.document.write('.text-danger { color: #ef4444; }');

    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    
    // Add a title to the printed report
    printWindow.document.write('<h1 style="text-align: center;">Student Report: ' + studentName + '</h1>');
    
    // Write the actual report content (from getstudentreport.php)
    printWindow.document.write(content);
    
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    
    // Print the window
    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
    };
}


document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){ closeStudentModal(); }
});
document.getElementById('closeModalBtn').addEventListener('click', function(e){ e.preventDefault(); closeStudentModal(); });
document.getElementById('modalBackdrop').addEventListener('click', function(){ closeStudentModal(); });

function closeStudentModal() {
    document.getElementById('modalBackdrop').classList.remove('active');
    document.getElementById('studentModal').classList.remove('active');
}

function exportToPDF() {
    window.print();
}
</script>

<?php include "includes/footer.php"; ?>