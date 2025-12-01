<?php
include "session.php";
include "pdo_functions.php";
require_login();
if($_SESSION['role']!=='student'){ header('Location: index.php'); exit; }

$page_title = 'My Profile';
$crud = new pdoCRUD();
$acc = $crud->getAccountById($_SESSION['account_id']);
$s = $crud->getAccountPerson('student',$acc['person_id']);

include "dbconfig.php";

$section = '';
if($s['advisory_section_id']){
    $stmt = $pdo->prepare("SELECT name FROM sections WHERE id = :id");
    $stmt->execute([':id'=>$s['advisory_section_id']]);
    $row = $stmt->fetch();
    $section = $row? $row['name'] : '';
}

$student_name = htmlspecialchars($s['family_name'].', '.$s['first_name'].' '.($s['middle_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$student_initials = strtoupper(substr($s['first_name'],0,1) . substr($s['family_name'],0,1));

$breadcrumb = [
    ['title' => 'My Profile', 'active' => true]
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
                        <h1 class="page-header-title">My Profile</h1>
                        <p class="page-header-subtitle">View your personal and academic information.</p>
                    </div>
                    <div class="page-header-actions">
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
            
            <div class="card" style="margin-bottom: var(--spacing-6);">
                <div class="card-body">
                    <div class="d-flex gap-6" style="align-items: center; flex-wrap: wrap;">
                        <div class="avatar avatar-primary" style="width: 100px; height: 100px; font-size: var(--font-size-3xl);"><?php echo $student_initials; ?></div>
                        <div style="flex: 1;">
                            <h2 style="margin: 0 0 var(--spacing-2) 0; font-size: var(--font-size-2xl);"><?php echo $student_name; ?></h2>
                            <div class="d-flex gap-4 flex-wrap" style="margin-top: var(--spacing-3);">
                                <span class="badge badge-primary"><?php echo htmlspecialchars($s['department'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="badge badge-info"><?php echo htmlspecialchars($s['grade_level'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php if($s['strand']): ?>
                                <span class="badge badge-accent"><?php echo htmlspecialchars($s['strand'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                                <?php if($section): ?>
                                <span class="badge badge-secondary"><?php echo htmlspecialchars($section, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--spacing-2); vertical-align: middle;">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Personal Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row" style="margin-bottom: var(--spacing-4);">
                            <div>
                                <label class="form-label text-muted text-sm">LRN</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['lrn'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                            <div>
                                <label class="form-label text-muted text-sm">Sex</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['sex'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom: var(--spacing-4);">
                            <div>
                                <label class="form-label text-muted text-sm">Birthdate</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['birthdate'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                            <div>
                                <label class="form-label text-muted text-sm">Birthplace</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['birthplace'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom: var(--spacing-4);">
                            <div>
                                <label class="form-label text-muted text-sm">Religion</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['religion'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                            <div>
                                <label class="form-label text-muted text-sm">Civil Status</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['civil_status'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div>
                                <label class="form-label text-muted text-sm">Mobile</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['mobile'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                            <div>
                                <label class="form-label text-muted text-sm">Email</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--spacing-2); vertical-align: middle;">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                            Academic Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row" style="margin-bottom: var(--spacing-4);">
                            <div>
                                <label class="form-label text-muted text-sm">Department</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['department'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                            <div>
                                <label class="form-label text-muted text-sm">Grade Level</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['grade_level'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom: var(--spacing-4);">
                            <div>
                                <label class="form-label text-muted text-sm">Section</label>
                                <div class="font-medium"><?php echo htmlspecialchars($section ?: 'Not Assigned', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                            <div>
                                <label class="form-label text-muted text-sm">Strand</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['strand'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div>
                                <label class="form-label text-muted text-sm">Student Type</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['student_type'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid-2" style="margin-top: var(--spacing-6);">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--spacing-2); vertical-align: middle;">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Address Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom: var(--spacing-5);">
                            <label class="form-label text-muted text-sm">Current Address</label>
                            <div class="font-medium">
                                <?php 
                                $curr_address = trim(($s['curr_house_street'] ?? '').' '.($s['curr_barangay'] ?? '').' '.($s['curr_city'] ?? '').' '.($s['curr_province'] ?? '').' '.($s['curr_zip'] ?? ''));
                                echo htmlspecialchars($curr_address ?: 'N/A', ENT_QUOTES, 'UTF-8'); 
                                ?>
                            </div>
                        </div>
                        <div>
                            <label class="form-label text-muted text-sm">Permanent Address</label>
                            <div class="font-medium">
                                <?php 
                                $perm_address = trim(($s['perm_house_street'] ?? '').' '.($s['perm_barangay'] ?? '').' '.($s['perm_city'] ?? '').' '.($s['perm_province'] ?? '').' '.($s['perm_zip'] ?? ''));
                                echo htmlspecialchars($perm_address ?: 'N/A', ENT_QUOTES, 'UTF-8'); 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--spacing-2); vertical-align: middle;">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Guardian Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row" style="margin-bottom: var(--spacing-4);">
                            <div>
                                <label class="form-label text-muted text-sm">Guardian Name</label>
                                <div class="font-medium">
                                    <?php 
                                    $guardian_name = trim(($s['guardian_first_name'] ?? '').' '.($s['guardian_middle_name'] ?? '').' '.($s['guardian_last_name'] ?? ''));
                                    echo htmlspecialchars($guardian_name ?: 'N/A', ENT_QUOTES, 'UTF-8'); 
                                    ?>
                                </div>
                            </div>
                            <div>
                                <label class="form-label text-muted text-sm">Relationship</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['guardian_relationship'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom: var(--spacing-4);">
                            <div>
                                <label class="form-label text-muted text-sm">Contact Number</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['guardian_contact'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                            <div>
                                <label class="form-label text-muted text-sm">Occupation</label>
                                <div class="font-medium"><?php echo htmlspecialchars($s['guardian_occupation'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                        <div>
                            <label class="form-label text-muted text-sm">Address</label>
                            <div class="font-medium"><?php echo htmlspecialchars($s['guardian_address'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card" style="margin-top: var(--spacing-6);">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--spacing-2); vertical-align: middle;">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        Educational Background
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-container" style="border: none; box-shadow: none;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>School Name</th>
                                    <th>Address</th>
                                    <th>Year Graduated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="font-medium">Elementary</span></td>
                                    <td><?php echo htmlspecialchars($s['elem_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($s['elem_address'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($s['elem_year_graduated'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="font-medium">Junior High School</span></td>
                                    <td><?php echo htmlspecialchars($s['jhs_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($s['jhs_address'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($s['jhs_year_graduated'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <?php if($s['last_school_name']): ?>
                                <tr>
                                    <td><span class="font-medium">Last School Attended</span></td>
                                    <td><?php echo htmlspecialchars($s['last_school_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($s['last_school_address'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>-</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>
