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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Student</title>
    <style>body{font-family: Arial, sans-serif;} .container{max-width:900px;margin:20px auto;padding:10px;} .grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;} input,select{width:100%;padding:8px;} .full{grid-column:1/3;} .section{margin-top:20px;border-top:1px solid #eee;padding-top:10px;}</style>
</head>
<body>
<div class="container">
    <h3>Update Student</h3>
    <?php if(isset($error)){ echo '<div style="color:red;" class="full">'.htmlspecialchars($error,ENT_QUOTES,'UTF-8').'</div>'; } ?>
    <form method="post">
        <div class="section"><div class="grid">
            <div><label>Department</label><select name="department" required><option <?php echo $s['department']=='JHS'?'selected':''; ?>>JHS</option><option <?php echo $s['department']=='SHS'?'selected':''; ?>>SHS</option></select></div>
            <div><label>Grade Level</label><select name="grade_level" required>
                <?php $grades=['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12']; foreach($grades as $g){ echo '<option '.($s['grade_level']==$g?'selected':'').'>'.$g.'</option>'; } ?>
            </select></div>
            <div><label>Strand (SHS)</label><select name="strand"><option value="">None</option><option <?php echo $s['strand']=='HUMSS'?'selected':''; ?>>HUMSS</option><option <?php echo $s['strand']=='TVL'?'selected':''; ?>>TVL</option></select></div>
            <div><label>Student Type</label><select name="student_type" required>
                <?php $types=['Old Student','New Student','Transferee']; foreach($types as $t){ echo '<option '.($s['student_type']==$t?'selected':'').'>'.$t.'</option>'; } ?>
            </select></div>
        </div></div>
        <div class="section"><div class="grid"><div class="full"><strong>Academic Information</strong></div><div class="full"><label>LRN</label><input type="text" name="lrn" value="<?php echo htmlspecialchars($s['lrn'],ENT_QUOTES,'UTF-8'); ?>" required></div></div></div>
        <div class="section"><div class="grid">
            <div><label>Family Name</label><input type="text" name="family_name" value="<?php echo htmlspecialchars($s['family_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>First Name</label><input type="text" name="first_name" value="<?php echo htmlspecialchars($s['first_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Middle Name</label><input type="text" name="middle_name" value="<?php echo htmlspecialchars($s['middle_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Suffix</label><input type="text" name="suffix" value="<?php echo htmlspecialchars($s['suffix']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
            <div><label>Birthdate</label><input type="date" name="birthdate" value="<?php echo htmlspecialchars($s['birthdate'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Birthplace</label><input type="text" name="birthplace" value="<?php echo htmlspecialchars($s['birthplace'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Religion</label><input type="text" name="religion" value="<?php echo htmlspecialchars($s['religion'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Civil Status</label><input type="text" name="civil_status" value="<?php echo htmlspecialchars($s['civil_status'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Sex</label><select name="sex" required><option <?php echo $s['sex']=='Male'?'selected':''; ?>>Male</option><option <?php echo $s['sex']=='Female'?'selected':''; ?>>Female</option></select></div>
        </div></div>
        <div class="section"><div class="grid">
            <div><label>Mobile Number</label><input type="text" name="mobile" value="<?php echo htmlspecialchars($s['mobile'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Email Address</label><input type="email" name="email" value="<?php echo htmlspecialchars($s['email'],ENT_QUOTES,'UTF-8'); ?>" required></div>
        </div></div>
        <div class="section"><div class="grid">
            <div class="full"><strong>Current Address</strong></div>
            <div><label>House No. & Street</label><input id="curr_house_street" type="text" name="curr_house_street" value="<?php echo htmlspecialchars($s['curr_house_street'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Barangay</label><input id="curr_barangay" type="text" name="curr_barangay" value="<?php echo htmlspecialchars($s['curr_barangay'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>City/Municipality</label><input id="curr_city" type="text" name="curr_city" value="<?php echo htmlspecialchars($s['curr_city'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Province</label><input id="curr_province" type="text" name="curr_province" value="<?php echo htmlspecialchars($s['curr_province'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>ZIP Code</label><input id="curr_zip" type="text" name="curr_zip" value="<?php echo htmlspecialchars($s['curr_zip'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div class="full"><strong>Permanent Address</strong></div>
            <div><label>House No. & Street</label><input id="perm_house_street" type="text" name="perm_house_street" value="<?php echo htmlspecialchars($s['perm_house_street'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Barangay</label><input id="perm_barangay" type="text" name="perm_barangay" value="<?php echo htmlspecialchars($s['perm_barangay'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>City/Municipality</label><input id="perm_city" type="text" name="perm_city" value="<?php echo htmlspecialchars($s['perm_city'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Province</label><input id="perm_province" type="text" name="perm_province" value="<?php echo htmlspecialchars($s['perm_province'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>ZIP Code</label><input id="perm_zip" type="text" name="perm_zip" value="<?php echo htmlspecialchars($s['perm_zip'],ENT_QUOTES,'UTF-8'); ?>" required></div>
        </div></div>
        <div class="section"><div class="grid">
            <div class="full"><strong>Educational Background</strong></div>
            <div><label>Elem Name</label><input type="text" name="elem_name" value="<?php echo htmlspecialchars($s['elem_name']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
            <div><label>Elem Address</label><input type="text" name="elem_address" value="<?php echo htmlspecialchars($s['elem_address']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
            <div><label>Elem Year Graduated</label><input type="text" name="elem_year_graduated" value="<?php echo htmlspecialchars($s['elem_year_graduated']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
            <div><label>Last School Name</label><input type="text" name="last_school_name" value="<?php echo htmlspecialchars($s['last_school_name']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
            <div><label>Last School Address</label><input type="text" name="last_school_address" value="<?php echo htmlspecialchars($s['last_school_address']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
            <div><label>JHS Name</label><input type="text" name="jhs_name" value="<?php echo htmlspecialchars($s['jhs_name']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
            <div><label>JHS Address</label><input type="text" name="jhs_address" value="<?php echo htmlspecialchars($s['jhs_address']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
            <div><label>JHS Year Graduated</label><input type="text" name="jhs_year_graduated" value="<?php echo htmlspecialchars($s['jhs_year_graduated']??'',ENT_QUOTES,'UTF-8'); ?>"></div>
        </div></div>
        <div class="section"><div class="grid">
            <div class="full"><strong>Guardian Information</strong></div>
            <div><label>Last Name</label><input type="text" name="guardian_last_name" value="<?php echo htmlspecialchars($s['guardian_last_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>First Name</label><input type="text" name="guardian_first_name" value="<?php echo htmlspecialchars($s['guardian_first_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Middle Name</label><input type="text" name="guardian_middle_name" value="<?php echo htmlspecialchars($s['guardian_middle_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Contact Number</label><input type="text" name="guardian_contact" value="<?php echo htmlspecialchars($s['guardian_contact'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Occupation</label><input type="text" name="guardian_occupation" value="<?php echo htmlspecialchars($s['guardian_occupation'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div class="full"><label>Address</label><input type="text" name="guardian_address" value="<?php echo htmlspecialchars($s['guardian_address'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div class="full"><label>Relationship</label><input type="text" name="guardian_relationship" value="<?php echo htmlspecialchars($s['guardian_relationship'],ENT_QUOTES,'UTF-8'); ?>" required></div>
        </div></div>
        <div class="section"><div class="grid">
            <div class="full"><strong>Parents Information</strong></div>
            <div><label>Mother Last Name</label><input type="text" name="mother_last_name" value="<?php echo htmlspecialchars($s['mother_last_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Mother First Name</label><input type="text" name="mother_first_name" value="<?php echo htmlspecialchars($s['mother_first_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Mother Middle Name</label><input type="text" name="mother_middle_name" value="<?php echo htmlspecialchars($s['mother_middle_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Mother Contact</label><input type="text" name="mother_contact" value="<?php echo htmlspecialchars($s['mother_contact'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Mother Occupation</label><input type="text" name="mother_occupation" value="<?php echo htmlspecialchars($s['mother_occupation'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div class="full"><label>Mother Address</label><input type="text" name="mother_address" value="<?php echo htmlspecialchars($s['mother_address'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Father Last Name</label><input type="text" name="father_last_name" value="<?php echo htmlspecialchars($s['father_last_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Father First Name</label><input type="text" name="father_first_name" value="<?php echo htmlspecialchars($s['father_first_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Father Middle Name</label><input type="text" name="father_middle_name" value="<?php echo htmlspecialchars($s['father_middle_name'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Father Contact</label><input type="text" name="father_contact" value="<?php echo htmlspecialchars($s['father_contact'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div><label>Father Occupation</label><input type="text" name="father_occupation" value="<?php echo htmlspecialchars($s['father_occupation'],ENT_QUOTES,'UTF-8'); ?>" required></div>
            <div class="full"><label>Father Address</label><input type="text" name="father_address" value="<?php echo htmlspecialchars($s['father_address'],ENT_QUOTES,'UTF-8'); ?>" required></div>
        </div></div>
        <div class="section"><button name="update_student">Update Student</button></div>
    </form>
    <p><a href="students.php">Back</a></p>
</div>
</body>
</html>

