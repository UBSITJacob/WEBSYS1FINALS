<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
$pdo = new pdoCRUD();
$id = (int)($_GET['id'] ?? 0);
$s = $pdo->getStudentById($id);
if(!$s){ echo 'Not found'; exit; }
if(isset($_POST['update_student'])){
    $data = [
        ':lrn'=>trim($_POST['lrn']), ':department'=>$_POST['department'], ':grade_level'=>$_POST['grade_level'], ':strand'=>($_POST['strand'] ?? null) !== '' ? $_POST['strand'] : null, ':student_type'=>$_POST['student_type'],
        ':family_name'=>trim($_POST['family_name']), ':first_name'=>trim($_POST['first_name']), ':middle_name'=>trim($_POST['middle_name']), ':suffix'=>($_POST['suffix'] ?? null), ':birthdate'=>$_POST['birthdate'], ':birthplace'=>trim($_POST['birthplace']), ':religion'=>trim($_POST['religion']), ':civil_status'=>trim($_POST['civil_status']), ':sex'=>$_POST['sex'],
        ':mobile'=>trim($_POST['mobile']), ':email'=>trim($_POST['email']),
        ':curr_house_street'=>trim($_POST['curr_house_street']), ':curr_barangay'=>trim($_POST['curr_barangay']), ':curr_city'=>trim($_POST['curr_city']), ':curr_province'=>trim($_POST['curr_province']), ':curr_zip'=>trim($_POST['curr_zip']),
        ':perm_house_street'=>trim($_POST['perm_house_street']), ':perm_barangay'=>trim($_POST['perm_barangay']), ':perm_city'=>trim($_POST['perm_city']), ':perm_province'=>trim($_POST['perm_province']), ':perm_zip'=>trim($_POST['perm_zip']),
        ':elem_name'=>($_POST['elem_name'] ?? null), ':elem_address'=>($_POST['elem_address'] ?? null), ':elem_year_graduated'=>($_POST['elem_year_graduated'] ?? null),
        ':last_school_name'=>($_POST['last_school_name'] ?? null), ':last_school_address'=>($_POST['last_school_address'] ?? null),
        ':jhs_name'=>($_POST['jhs_name'] ?? null), ':jhs_address'=>($_POST['jhs_address'] ?? null), ':jhs_year_graduated'=>($_POST['jhs_year_graduated'] ?? null),
        ':guardian_last_name'=>trim($_POST['guardian_last_name']), ':guardian_first_name'=>trim($_POST['guardian_first_name']), ':guardian_middle_name'=>trim($_POST['guardian_middle_name']), ':guardian_contact'=>trim($_POST['guardian_contact']), ':guardian_occupation'=>trim($_POST['guardian_occupation']), ':guardian_address'=>trim($_POST['guardian_address']), ':guardian_relationship'=>trim($_POST['guardian_relationship']),
        ':mother_last_name'=>trim($_POST['mother_last_name']), ':mother_first_name'=>trim($_POST['mother_first_name']), ':mother_middle_name'=>trim($_POST['mother_middle_name']), ':mother_contact'=>trim($_POST['mother_contact']), ':mother_occupation'=>trim($_POST['mother_occupation']), ':mother_address'=>trim($_POST['mother_address']),
        ':father_last_name'=>trim($_POST['father_last_name']), ':father_first_name'=>trim($_POST['father_first_name']), ':father_middle_name'=>trim($_POST['father_middle_name']), ':father_contact'=>trim($_POST['father_contact']), ':father_occupation'=>trim($_POST['father_occupation']), ':father_address'=>trim($_POST['father_address'])
    ];
    $ok = $pdo->updateStudent($id,$data);
    if($ok){ echo "<script>alert('Student updated');window.location.href='students.php';</script>"; exit; } else { $error = "Unable to update"; }
}
$page_title = 'Update Student';
$breadcrumb = [ ['title'=>'Students','url'=>'students.php'], ['title'=>'Update','active'=>true] ];
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
                        <h1 class="page-header-title">Update Student</h1>
                        <p class="page-header-subtitle">Edit student profile and academic information</p>
                    </div>
                </div>
            </div>
            <?php if(isset($error)){ echo '<div class="alert">'.htmlspecialchars($error,ENT_QUOTES,'UTF-8').'</div>'; } ?>
            <div class="card">
                <div class="card-header"><h3 class="card-title">Student Information</h3></div>
                <div class="card-body">
                    <form method="post" class="form">
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label required">Department</label>
                                <select name="department" class="form-control" required>
                                    <option <?php echo $s['department']=='JHS'?'selected':''; ?>>JHS</option>
                                    <option <?php echo $s['department']=='SHS'?'selected':''; ?>>SHS</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Grade Level</label>
                                <select name="grade_level" class="form-control" required>
                                    <?php $grades=['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12']; foreach($grades as $g){ echo '<option '.($s['grade_level']==$g?'selected':'').'>'.$g.'</option>'; } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label">Strand (SHS)</label>
                                <select name="strand" class="form-control"><option value="">None</option><option <?php echo $s['strand']=='HUMSS'?'selected':''; ?>>HUMSS</option><option <?php echo $s['strand']=='TVL'?'selected':''; ?>>TVL</option></select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Student Type</label>
                                <select name="student_type" class="form-control" required>
                                    <?php $types=['Old Student','New Student','Transferee']; foreach($types as $t){ echo '<option '.($s['student_type']==$t?'selected':'').'>'.$t.'</option>'; } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">LRN</label>
                                <input type="text" name="lrn" class="form-control" value="<?php echo htmlspecialchars($s['lrn'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">Family Name</label>
                                <input type="text" name="family_name" class="form-control" value="<?php echo htmlspecialchars($s['family_name'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($s['first_name'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" value="<?php echo htmlspecialchars($s['middle_name'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label">Suffix</label>
                                <input type="text" name="suffix" class="form-control" value="<?php echo htmlspecialchars($s['suffix']??'',ENT_QUOTES,'UTF-8'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Birthdate</label>
                                <input type="date" name="birthdate" class="form-control" value="<?php echo htmlspecialchars($s['birthdate'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Birthplace</label>
                                <input type="text" name="birthplace" class="form-control" value="<?php echo htmlspecialchars($s['birthplace'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">Religion</label>
                                <input type="text" name="religion" class="form-control" value="<?php echo htmlspecialchars($s['religion'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Civil Status</label>
                                <input type="text" name="civil_status" class="form-control" value="<?php echo htmlspecialchars($s['civil_status'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Sex</label>
                                <select name="sex" class="form-control" required><option <?php echo $s['sex']=='Male'?'selected':''; ?>>Male</option><option <?php echo $s['sex']=='Female'?'selected':''; ?>>Female</option></select>
                            </div>
                        </div>
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label required">Mobile Number</label>
                                <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($s['mobile'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($s['email'],ENT_QUOTES,'UTF-8'); ?>" required>
                            </div>
                        </div>
                        <div class="form-row"><div class="form-group"><h4>Current Address</h4></div></div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">House No. & Street</label><input type="text" name="curr_house_street" class="form-control" value="<?php echo htmlspecialchars($s['curr_house_street'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Barangay</label><input type="text" name="curr_barangay" class="form-control" value="<?php echo htmlspecialchars($s['curr_barangay'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">City/Municipality</label><input type="text" name="curr_city" class="form-control" value="<?php echo htmlspecialchars($s['curr_city'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">Province</label><input type="text" name="curr_province" class="form-control" value="<?php echo htmlspecialchars($s['curr_province'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">ZIP Code</label><input type="text" name="curr_zip" class="form-control" value="<?php echo htmlspecialchars($s['curr_zip'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row"><div class="form-group"><h4>Permanent Address</h4></div></div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">House No. & Street</label><input type="text" name="perm_house_street" class="form-control" value="<?php echo htmlspecialchars($s['perm_house_street'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Barangay</label><input type="text" name="perm_barangay" class="form-control" value="<?php echo htmlspecialchars($s['perm_barangay'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">City/Municipality</label><input type="text" name="perm_city" class="form-control" value="<?php echo htmlspecialchars($s['perm_city'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">Province</label><input type="text" name="perm_province" class="form-control" value="<?php echo htmlspecialchars($s['perm_province'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">ZIP Code</label><input type="text" name="perm_zip" class="form-control" value="<?php echo htmlspecialchars($s['perm_zip'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row"><div class="form-group"><h4>Educational Background</h4></div></div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label">Elem Name</label><input type="text" name="elem_name" class="form-control" value="<?php echo htmlspecialchars($s['elem_name']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
                            <div class="form-group"><label class="form-label">Elem Address</label><input type="text" name="elem_address" class="form-control" value="<?php echo htmlspecialchars($s['elem_address']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
                            <div class="form-group"><label class="form-label">Elem Year Graduated</label><input type="text" name="elem_year_graduated" class="form-control" value="<?php echo htmlspecialchars($s['elem_year_graduated']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label">Last School Name</label><input type="text" name="last_school_name" class="form-control" value="<?php echo htmlspecialchars($s['last_school_name']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
                            <div class="form-group"><label class="form-label">Last School Address</label><input type="text" name="last_school_address" class="form-control" value="<?php echo htmlspecialchars($s['last_school_address']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label">JHS Name</label><input type="text" name="jhs_name" class="form-control" value="<?php echo htmlspecialchars($s['jhs_name']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
                            <div class="form-group"><label class="form-label">JHS Address</label><input type="text" name="jhs_address" class="form-control" value="<?php echo htmlspecialchars($s['jhs_address']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
                            <div class="form-group"><label class="form-label">JHS Year Graduated</label><input type="text" name="jhs_year_graduated" class="form-control" value="<?php echo htmlspecialchars($s['jhs_year_graduated']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
                        </div>
                        <div class="form-row"><div class="form-group"><h4>Guardian Information</h4></div></div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">Last Name</label><input type="text" name="guardian_last_name" class="form-control" value="<?php echo htmlspecialchars($s['guardian_last_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">First Name</label><input type="text" name="guardian_first_name" class="form-control" value="<?php echo htmlspecialchars($s['guardian_first_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Middle Name</label><input type="text" name="guardian_middle_name" class="form-control" value="<?php echo htmlspecialchars($s['guardian_middle_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">Contact Number</label><input type="text" name="guardian_contact" class="form-control" value="<?php echo htmlspecialchars($s['guardian_contact'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Occupation</label><input type="text" name="guardian_occupation" class="form-control" value="<?php echo htmlspecialchars($s['guardian_occupation'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="flex:1"><label class="form-label required">Address</label><input type="text" name="guardian_address" class="form-control" value="<?php echo htmlspecialchars($s['guardian_address'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="flex:1"><label class="form-label required">Relationship</label><input type="text" name="guardian_relationship" class="form-control" value="<?php echo htmlspecialchars($s['guardian_relationship'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row"><div class="form-group"><h4>Parents Information</h4></div></div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">Mother Last Name</label><input type="text" name="mother_last_name" class="form-control" value="<?php echo htmlspecialchars($s['mother_last_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Mother First Name</label><input type="text" name="mother_first_name" class="form-control" value="<?php echo htmlspecialchars($s['mother_first_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Mother Middle Name</label><input type="text" name="mother_middle_name" class="form-control" value="<?php echo htmlspecialchars($s['mother_middle_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">Mother Contact</label><input type="text" name="mother_contact" class="form-control" value="<?php echo htmlspecialchars($s['mother_contact'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Mother Occupation</label><input type="text" name="mother_occupation" class="form-control" value="<?php echo htmlspecialchars($s['mother_occupation'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="flex:1"><label class="form-label required">Mother Address</label><input type="text" name="mother_address" class="form-control" value="<?php echo htmlspecialchars($s['mother_address'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">Father Last Name</label><input type="text" name="father_last_name" class="form-control" value="<?php echo htmlspecialchars($s['father_last_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Father First Name</label><input type="text" name="father_first_name" class="form-control" value="<?php echo htmlspecialchars($s['father_first_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Father Middle Name</label><input type="text" name="father_middle_name" class="form-control" value="<?php echo htmlspecialchars($s['father_middle_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group"><label class="form-label required">Father Contact</label><input type="text" name="father_contact" class="form-control" value="<?php echo htmlspecialchars($s['father_contact'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                            <div class="form-group"><label class="form-label required">Father Occupation</label><input type="text" name="father_occupation" class="form-control" value="<?php echo htmlspecialchars($s['father_occupation'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-row">
                            <div class="form-group" style="flex:1"><label class="form-label required">Father Address</label><input type="text" name="father_address" class="form-control" value="<?php echo htmlspecialchars($s['father_address'],ENT_QUOTES,'UTF-8'); ?>" required></div>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-primary" name="update_student">Update Student</button>
                            <a class="btn btn-secondary" href="students.php">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>
