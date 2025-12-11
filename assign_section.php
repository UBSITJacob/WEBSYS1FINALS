<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
include "dbconfig.php";
$students = $pdo->query("SELECT id, CONCAT(family_name, ', ', first_name) AS name FROM students ORDER BY family_name")->fetchAll();
$sections = $pdo->query("SELECT id, name FROM sections ORDER BY name")->fetchAll();
if(isset($_POST['assign'])){
    $sid = (int)($_POST['student_id'] ?? 0);
    $sec = (int)($_POST['section_id'] ?? 0);
    $crud = new pdoCRUD();
    $ok = $crud->assignStudentSection($sid,$sec);
    $msg = $ok? 'Assigned' : 'Section full or invalid';
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
                        <h1 class="page-header-title">Assign Section</h1>
                        <p class="page-header-subtitle">Assign a student to an advisory section</p>
                    </div>
                </div>
            </div>

            <?php if(isset($msg)){ echo '<div class="alert">'.htmlspecialchars($msg,ENT_QUOTES,'UTF-8').'</div>'; } ?>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Assignment</h3></div>
                <div class="card-body">
                    <form method="post" class="form">
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label required">Student</label>
                                <select name="student_id" class="form-control" required>
                                    <?php foreach($students as $s){ echo '<option value="'.$s['id'].'">'.htmlspecialchars($s['name'],ENT_QUOTES,'UTF-8').'</option>'; } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Section</label>
                                <select name="section_id" class="form-control" required>
                                    <?php foreach($sections as $sec){ echo '<option value="'.$sec['id'].'">'.htmlspecialchars($sec['name'],ENT_QUOTES,'UTF-8').'</option>'; } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-primary" name="assign">Assign Section</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>
