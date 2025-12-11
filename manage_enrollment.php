<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
include "dbconfig.php";
$students = $pdo->query("SELECT id, CONCAT(family_name, ', ', first_name) AS name FROM students ORDER BY family_name")->fetchAll();
if(isset($_GET['student_id'])){
    $sid = (int)$_GET['student_id'];
    $crud = new pdoCRUD();
    $enrollments = $crud->getEnrollmentsByStudent($sid);
    $loads = $pdo->query("SELECT sl.id, CONCAT(s.code,' - ',s.name,' (',sec.name,')') AS label FROM subject_loads sl JOIN subjects s ON sl.subject_id=s.id JOIN sections sec ON sl.section_id=sec.id ORDER BY s.name")->fetchAll();
}
if(isset($_POST['add'])){
    $sid = (int)$_POST['student_id'];
    $lid = (int)$_POST['subject_load_id'];
    $sy = trim($_POST['school_year']);
    $sem = $_POST['semester'] ?? null;
    $crud = new pdoCRUD();
    $ok = $crud->addEnrollment($sid,$lid,$sy,$sem);
    $msg = $ok? 'Enrolled' : 'Failed';
}
if(isset($_POST['remove'])){
    $eid = (int)$_POST['enrollment_id'];
    $crud = new pdoCRUD();
    $ok = $crud->deleteEnrollment($eid);
    $msg = $ok? 'Removed' : 'Failed';
}
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
                        <h1 class="page-header-title">Manage Enrollment</h1>
                        <p class="page-header-subtitle">View and modify student enrollments</p>
                    </div>
                </div>
            </div>

            <?php if(isset($msg)){ echo '<div class="alert">'.htmlspecialchars($msg,ENT_QUOTES,'UTF-8').'</div>'; } ?>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Select Student</h3></div>
                <div class="card-body">
                    <form method="get" class="form">
                        <div class="form-row">
                            <div class="form-group" style="flex:1">
                                <label class="form-label required">Student</label>
                                <select name="student_id" class="form-control" required>
                                    <?php foreach($students as $s){ echo '<option value="'.$s['id'].'"'.(isset($sid)&&$sid==$s['id']?' selected':'').'>'.htmlspecialchars($s['name'],ENT_QUOTES,'UTF-8').'</option>'; } ?>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button class="btn btn-primary">Load</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if(isset($sid)){ ?>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Current Enrollments</h3></div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr><th>Subject</th><th>Section</th><th>School Year</th><th>Semester</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php if(!$enrollments){ echo '<tr><td colspan="5" align="center">None</td></tr>'; } else { foreach($enrollments as $e){ echo '<tr><td>'.htmlspecialchars($e['subject_name'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($e['section_name'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($e['school_year'],ENT_QUOTES,'UTF-8').'</td><td>'.htmlspecialchars($e['semester']??'',ENT_QUOTES,'UTF-8').'</td><td><form method="post"><input type="hidden" name="enrollment_id" value="'.$e['id'].'"><button class="btn btn-sm btn-danger" name="remove">Remove</button></form></td></tr>'; } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Add Enrollment</h3></div>
                <div class="card-body">
                    <form method="post" class="form">
                        <input type="hidden" name="student_id" value="<?php echo (int)$sid; ?>">
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">Subject Load</label>
                                <select name="subject_load_id" class="form-control" required><?php foreach($loads as $l){ echo '<option value="'.$l['id'].'">'.htmlspecialchars($l['label'],ENT_QUOTES,'UTF-8').'</option>'; } ?></select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">School Year</label>
                                <input type="text" name="school_year" class="form-control" placeholder="2025-2026" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Semester</label>
                                <select name="semester" class="form-control"><option value="">None</option><option>First</option><option>Second</option></select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-primary" name="add">Add Enrollment</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php } ?>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>
