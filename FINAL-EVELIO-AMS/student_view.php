<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
$pdo = new pdoCRUD();
$s = $pdo->getStudentById($id);
if(!$s){ echo 'Student not found'; exit; }

$page_title = 'Student Details';
$breadcrumb = [
    ['title' => 'Students', 'url' => 'students.php'],
    ['title' => 'View Student', 'active' => true]
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
                        <h1 class="page-header-title"><?php echo htmlspecialchars($s['family_name'] . ', ' . $s['first_name'] . ' ' . $s['middle_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                        <p class="page-header-subtitle">Student ID: <?php echo htmlspecialchars($s['lrn'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="page-header-actions">
                        <a href="student_update.php?id=<?php echo $id; ?>" class="btn btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Edit Student
                        </a>
                        <a href="students.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                            Academic Information
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="font-medium text-secondary" style="width: 40%;">LRN</td>
                                    <td><?php echo htmlspecialchars($s['lrn'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Department</td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($s['department'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Grade Level</td>
                                    <td><?php echo htmlspecialchars($s['grade_level'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Strand</td>
                                    <td><?php echo htmlspecialchars($s['strand'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Student Type</td>
                                    <td><span class="badge badge-secondary"><?php echo htmlspecialchars($s['student_type'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Personal Information
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="font-medium text-secondary" style="width: 40%;">Full Name</td>
                                    <td><?php echo htmlspecialchars($s['family_name'] . ', ' . $s['first_name'] . ' ' . $s['middle_name'] . ($s['suffix'] ? ' ' . $s['suffix'] : ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Birthdate</td>
                                    <td><?php echo htmlspecialchars($s['birthdate'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Birthplace</td>
                                    <td><?php echo htmlspecialchars($s['birthplace'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Religion</td>
                                    <td><?php echo htmlspecialchars($s['religion'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Civil Status</td>
                                    <td><?php echo htmlspecialchars($s['civil_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Sex</td>
                                    <td><?php echo htmlspecialchars($s['sex'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        Contact Information
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="font-medium text-secondary" style="width: 20%;">Mobile</td>
                                <td><?php echo htmlspecialchars($s['mobile'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <td class="font-medium text-secondary">Email</td>
                                <td><?php echo htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <td class="font-medium text-secondary">Current Address</td>
                                <td><?php echo htmlspecialchars($s['curr_house_street'] . ', ' . $s['curr_barangay'] . ', ' . $s['curr_city'] . ', ' . $s['curr_province'] . ' ' . $s['curr_zip'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <tr>
                                <td class="font-medium text-secondary">Permanent Address</td>
                                <td><?php echo htmlspecialchars($s['perm_house_street'] . ', ' . $s['perm_barangay'] . ', ' . $s['perm_city'] . ', ' . $s['perm_province'] . ' ' . $s['perm_zip'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        Educational Background
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <tbody>
                            <?php if(!empty($s['elem_name'])): ?>
                            <tr>
                                <td class="font-medium text-secondary" style="width: 20%;">Elementary School</td>
                                <td><?php echo htmlspecialchars($s['elem_name'] . ' - ' . ($s['elem_address'] ?? '') . ' (' . ($s['elem_year_graduated'] ?? 'N/A') . ')', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if(!empty($s['last_school_name'])): ?>
                            <tr>
                                <td class="font-medium text-secondary">Last School Attended</td>
                                <td><?php echo htmlspecialchars($s['last_school_name'] . ' - ' . ($s['last_school_address'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if(!empty($s['jhs_name'])): ?>
                            <tr>
                                <td class="font-medium text-secondary">Junior High School</td>
                                <td><?php echo htmlspecialchars($s['jhs_name'] . ' - ' . ($s['jhs_address'] ?? '') . ' (' . ($s['jhs_year_graduated'] ?? 'N/A') . ')', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if(empty($s['elem_name']) && empty($s['last_school_name']) && empty($s['jhs_name'])): ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted p-6">No educational background information available</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="grid-2 mt-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Guardian Information
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="font-medium text-secondary" style="width: 40%;">Name</td>
                                    <td><?php echo htmlspecialchars($s['guardian_first_name'] . ' ' . $s['guardian_middle_name'] . ' ' . $s['guardian_last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Relationship</td>
                                    <td><?php echo htmlspecialchars($s['guardian_relationship'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Contact</td>
                                    <td><?php echo htmlspecialchars($s['guardian_contact'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Occupation</td>
                                    <td><?php echo htmlspecialchars($s['guardian_occupation'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Address</td>
                                    <td><?php echo htmlspecialchars($s['guardian_address'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Parents Information
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="font-medium text-secondary" style="width: 40%;">Mother</td>
                                    <td><?php echo htmlspecialchars($s['mother_first_name'] . ' ' . $s['mother_middle_name'] . ' ' . $s['mother_last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Mother Contact</td>
                                    <td><?php echo htmlspecialchars($s['mother_contact'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Mother Occupation</td>
                                    <td><?php echo htmlspecialchars($s['mother_occupation'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Father</td>
                                    <td><?php echo htmlspecialchars($s['father_first_name'] . ' ' . $s['father_middle_name'] . ' ' . $s['father_last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Father Contact</td>
                                    <td><?php echo htmlspecialchars($s['father_contact'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-secondary">Father Occupation</td>
                                    <td><?php echo htmlspecialchars($s['father_occupation'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>
