<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
$pdo = new pdoCRUD();
$a = $pdo->getApplicantById($id);
if(!$a){ echo 'Not found'; exit; }
$isAjax = isset($_GET['ajax']);
$page_title = 'Applicant View';
$breadcrumb = [ ['title'=>'Applicants','url'=>'applicants.php'], ['title'=>'View','active'=>true] ];
// Resolve linked student and account existence
$student = $pdo->getStudentByLRN($a['lrn']);
$student_id = $student['id'] ?? null;
$has_account = $student_id ? $pdo->hasStudentAccount((int)$student_id) : false;
?>
<?php if(!$isAjax){ include "includes/header.php"; } ?>
<?php if(!$isAjax){ echo '<div class="app-layout">'; include "includes/sidebar.php"; echo '<div class="main-wrapper">'; include "includes/topbar.php"; echo '<main class="main-content">'; } ?>
            <div class="page-header">
                <div class="page-header-row">
                    <div>
                        <h1 class="page-header-title">Applicant Details</h1>
                        <p class="page-header-subtitle">Review and take action on this application</p>
                    </div>
                    <div class="page-header-actions"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Profile</h3></div>
                <div class="card-body">
                    <div class="form-row form-row-3">
                        <div class="form-group"><label class="form-label">LRN</label><div><?php echo htmlspecialchars($a['lrn'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Department</label><div><?php echo htmlspecialchars($a['department'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Grade Level</label><div><?php echo htmlspecialchars($a['grade_level'],ENT_QUOTES,'UTF-8'); ?></div></div>
                    </div>
                    <div class="form-row form-row-3">
                        <div class="form-group"><label class="form-label">Strand</label><div><?php echo htmlspecialchars($a['strand'] ?? '',ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Student Type</label><div><?php echo htmlspecialchars($a['student_type'],ENT_QUOTES,'UTF-8'); ?></div></div>
                    </div>
                    <div class="form-row form-row-3">
                        <div class="form-group"><label class="form-label">Family Name</label><div><?php echo htmlspecialchars($a['family_name'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">First Name</label><div><?php echo htmlspecialchars($a['first_name'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Middle Name</label><div><?php echo htmlspecialchars($a['middle_name'],ENT_QUOTES,'UTF-8'); ?></div></div>
                    </div>
                    <div class="form-row form-row-3">
                        <div class="form-group"><label class="form-label">Birthdate</label><div><?php echo htmlspecialchars($a['birthdate'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Birthplace</label><div><?php echo htmlspecialchars($a['birthplace'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Religion</label><div><?php echo htmlspecialchars($a['religion'],ENT_QUOTES,'UTF-8'); ?></div></div>
                    </div>
                    <div class="form-row form-row-3">
                        <div class="form-group"><label class="form-label">Civil Status</label><div><?php echo htmlspecialchars($a['civil_status'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Sex</label><div><?php echo htmlspecialchars($a['sex'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Mobile</label><div><?php echo htmlspecialchars($a['mobile'],ENT_QUOTES,'UTF-8'); ?></div></div>
                    </div>
                    <div class="form-row form-row-2">
                        <div class="form-group"><label class="form-label">Email</label><div><?php echo htmlspecialchars($a['email'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Status Changed</label><div><?php echo htmlspecialchars($a['status_changed_at'] ?? '',ENT_QUOTES,'UTF-8'); ?></div></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Addresses</h3></div>
                <div class="card-body">
                    <div class="form-row form-row-2">
                        <div class="form-group" style="flex:1"><label class="form-label">Current Address</label><div><?php echo htmlspecialchars($a['curr_house_street'].' '.$a['curr_barangay'].' '.$a['curr_city'].' '.$a['curr_province'].' '.$a['curr_zip'],ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group" style="flex:1"><label class="form-label">Permanent Address</label><div><?php echo htmlspecialchars($a['perm_house_street'].' '.$a['perm_barangay'].' '.$a['perm_city'].' '.$a['perm_province'].' '.$a['perm_zip'],ENT_QUOTES,'UTF-8'); ?></div></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Education</h3></div>
                <div class="card-body">
                    <div class="form-row form-row-3">
                        <div class="form-group"><label class="form-label">Elementary</label><div><?php echo htmlspecialchars(($a['elem_name']??'').' '.($a['elem_address']??'').' '.($a['elem_year_graduated']??''),ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Last School</label><div><?php echo htmlspecialchars(($a['last_school_name']??'').' '.($a['last_school_address']??''),ENT_QUOTES,'UTF-8'); ?></div></div>
                        <div class="form-group"><label class="form-label">Junior High</label><div><?php echo htmlspecialchars(($a['jhs_name']??'').' '.($a['jhs_address']??'').' '.($a['jhs_year_graduated']??''),ENT_QUOTES,'UTF-8'); ?></div></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Family</h3></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group" style="flex:1">
                            <label class="form-label">Guardian</label>
                            <table class="table">
                                <thead>
                                    <tr><th>Name</th><th>Contact</th><th>Occupation</th><th>Address</th><th>Relationship</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo htmlspecialchars($a['guardian_last_name'].' '.$a['guardian_first_name'].' '.$a['guardian_middle_name'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['guardian_contact'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['guardian_occupation'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['guardian_address'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['guardian_relationship'],ENT_QUOTES,'UTF-8'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex:1">
                            <label class="form-label">Mother</label>
                            <table class="table">
                                <thead>
                                    <tr><th>Name</th><th>Contact</th><th>Occupation</th><th>Address</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo htmlspecialchars($a['mother_last_name'].' '.$a['mother_first_name'].' '.$a['mother_middle_name'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['mother_contact'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['mother_occupation'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['mother_address'],ENT_QUOTES,'UTF-8'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex:1">
                            <label class="form-label">Father</label>
                            <table class="table">
                                <thead>
                                    <tr><th>Name</th><th>Contact</th><th>Occupation</th><th>Address</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo htmlspecialchars($a['father_last_name'].' '.$a['father_first_name'].' '.$a['father_middle_name'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['father_contact'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['father_occupation'],ENT_QUOTES,'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($a['father_address'],ENT_QUOTES,'UTF-8'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(!$isAjax){ ?>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Actions</h3></div>
                <div class="card-body">
                    <div class="btn-group">
                        <button class="btn btn-success" onclick="approve(<?php echo (int)$id; ?>)">Approve</button>
                        <button class="btn btn-danger" onclick="decline(<?php echo (int)$id; ?>)">Decline</button>
                        <button class="btn btn-primary" onclick="createAccount(<?php echo (int)($student_id ?? 0); ?>)" 
                                <?php echo ($a['status']!=='approved' || !$student_id || $has_account) ? 'disabled' : ''; ?>>
                            Create Account
                        </button>
                        <a class="btn btn-secondary" href="applicants.php">Back</a>
                    </div>
                    <?php if($has_account): ?>
                        <div class="text-muted" style="margin-top: var(--spacing-2);">Account already exists for this student.</div>
                    <?php elseif($a['status']!=='approved'): ?>
                        <div class="text-muted" style="margin-top: var(--spacing-2);">Account creation is enabled after confirmation.</div>
                    <?php elseif(!$student_id): ?>
                        <div class="text-muted" style="margin-top: var(--spacing-2);">No student record found. Approve the applicant first.</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php } ?>
        </main>
    </div>
<?php if(!$isAjax){ echo '</main></div></div>'; } ?>
<?php if(!$isAjax){ ?>
<script>
function createAccount(studentId){
    if(!studentId){ showNotification('Missing student record', 'danger'); return; }
    fetch('crud/student.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=create_account&id=' + studentId
    })
    .then(r => r.json())
    .then(j => {
        if(j && j.success){
            showNotification('Account created successfully', 'success');
            setTimeout(() => { window.location.reload(); }, 500);
        } else {
            showNotification((j && j.message) ? j.message : 'Failed to create account', 'danger');
        }
    })
    .catch(e => { showNotification(e.message || 'Network error', 'danger'); });
}
</script>
<?php include "includes/footer.php"; } ?>
