<?php
include "pdo_functions.php";
$pdo = new pdoCRUD();

if(isset($_POST['submit_app'])){
    $req = ['department','grade_level','student_type','family_name','first_name','middle_name','birthdate','birthplace','religion','civil_status','sex','mobile','email','curr_house_street','curr_barangay','curr_city','curr_province','curr_zip','perm_house_street','perm_barangay','perm_city','perm_province','perm_zip','lrn','guardian_last_name','guardian_first_name','guardian_middle_name','guardian_contact','guardian_occupation','guardian_address','guardian_relationship','mother_last_name','mother_first_name','mother_middle_name','mother_contact','mother_occupation','mother_address','father_last_name','father_first_name','father_middle_name','father_contact','father_occupation','father_address'];
    foreach($req as $k){ if(empty($_POST[$k])){ $error = "Please complete all required fields"; break; } }
    if(!isset($error)){
        $data = [
            ':department'=>$_POST['department'],
            ':grade_level'=>$_POST['grade_level'],
            ':strand'=>($_POST['strand'] ?? null) !== '' ? $_POST['strand'] : null,
            ':student_type'=>$_POST['student_type'],
            ':family_name'=>trim($_POST['family_name']),
            ':first_name'=>trim($_POST['first_name']),
            ':middle_name'=>trim($_POST['middle_name']),
            ':suffix'=>($_POST['suffix'] ?? null) !== '' ? $_POST['suffix'] : null,
            ':birthdate'=>$_POST['birthdate'],
            ':birthplace'=>trim($_POST['birthplace']),
            ':religion'=>trim($_POST['religion']),
            ':civil_status'=>trim($_POST['civil_status']),
            ':sex'=>$_POST['sex'],
            ':mobile'=>trim($_POST['mobile']),
            ':email'=>trim($_POST['email']),
            ':curr_house_street'=>trim($_POST['curr_house_street']),
            ':curr_barangay'=>trim($_POST['curr_barangay']),
            ':curr_city'=>trim($_POST['curr_city']),
            ':curr_province'=>trim($_POST['curr_province']),
            ':curr_zip'=>trim($_POST['curr_zip']),
            ':perm_house_street'=>trim($_POST['perm_house_street']),
            ':perm_barangay'=>trim($_POST['perm_barangay']),
            ':perm_city'=>trim($_POST['perm_city']),
            ':perm_province'=>trim($_POST['perm_province']),
            ':perm_zip'=>trim($_POST['perm_zip']),
            ':elem_name'=>($_POST['elem_name'] ?? null),
            ':elem_address'=>($_POST['elem_address'] ?? null),
            ':elem_year_graduated'=>($_POST['elem_year_graduated'] ?? null),
            ':last_school_name'=>($_POST['last_school_name'] ?? null),
            ':last_school_address'=>($_POST['last_school_address'] ?? null),
            ':jhs_name'=>($_POST['jhs_name'] ?? null),
            ':jhs_address'=>($_POST['jhs_address'] ?? null),
            ':jhs_year_graduated'=>($_POST['jhs_year_graduated'] ?? null),
            ':lrn'=>trim($_POST['lrn']),
            ':guardian_last_name'=>trim($_POST['guardian_last_name']),
            ':guardian_first_name'=>trim($_POST['guardian_first_name']),
            ':guardian_middle_name'=>trim($_POST['guardian_middle_name']),
            ':guardian_contact'=>trim($_POST['guardian_contact']),
            ':guardian_occupation'=>trim($_POST['guardian_occupation']),
            ':guardian_address'=>trim($_POST['guardian_address']),
            ':guardian_relationship'=>trim($_POST['guardian_relationship']),
            ':mother_last_name'=>trim($_POST['mother_last_name']),
            ':mother_first_name'=>trim($_POST['mother_first_name']),
            ':mother_middle_name'=>trim($_POST['mother_middle_name']),
            ':mother_contact'=>trim($_POST['mother_contact']),
            ':mother_occupation'=>trim($_POST['mother_occupation']),
            ':mother_address'=>trim($_POST['mother_address']),
            ':father_last_name'=>trim($_POST['father_last_name']),
            ':father_first_name'=>trim($_POST['father_first_name']),
            ':father_middle_name'=>trim($_POST['father_middle_name']),
            ':father_contact'=>trim($_POST['father_contact']),
            ':father_occupation'=>trim($_POST['father_occupation']),
            ':father_address'=>trim($_POST['father_address'])
        ];
        $id = $pdo->insertApplicant($data);
        if($id){
            echo "<script>alert('Application submitted');window.location.href='index.php';</script>";
            exit;
        }else{
            $error = "Unable to submit";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Application</title>
    <style>
        body{font-family: Arial, sans-serif;}
        .container{max-width:900px;margin:20px auto;padding:10px;}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
        input,select{width:100%;padding:8px;}
        .full{grid-column:1/3;}
        .section{margin-top:20px;border-top:1px solid #eee;padding-top:10px;}
    </style>
    <script>
        function sameAsCurrent(cb){
            if(cb.checked){
                ['house_street','barangay','city','province','zip'].forEach(function(f){
                    document.getElementById('perm_'+f).value = document.getElementById('curr_'+f).value;
                });
            }
        }
    </script>
</head>
<body>
<div class="container">
    <h3>Apply as New Student</h3>
    <?php if(isset($error)){ echo '<div style="color:red;" class="full">'.htmlspecialchars($error,ENT_QUOTES,'UTF-8').'</div>'; } ?>
    <form method="post">
        <div class="section">
            <div class="grid">
                <div>
                    <label>Department</label>
                    <select name="department" required>
                        <option value="JHS">Junior High School</option>
                        <option value="SHS">Senior High School</option>
                    </select>
                </div>
                <div>
                    <label>Grade Level</label>
                    <select name="grade_level" required>
                        <option>Grade 7</option>
                        <option>Grade 8</option>
                        <option>Grade 9</option>
                        <option>Grade 10</option>
                        <option>Grade 11</option>
                        <option>Grade 12</option>
                    </select>
                </div>
                <div>
                    <label>Strand (SHS)</label>
                    <select name="strand">
                        <option value="">None</option>
                        <option>HUMSS</option>
                        <option>TVL</option>
                    </select>
                </div>
                <div>
                    <label>Student Type</label>
                    <select name="student_type" required>
                        <option>Old Student</option>
                        <option>New Student</option>
                        <option>Transferee</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="grid">
                <div><label>Family Name</label><input type="text" name="family_name" required></div>
                <div><label>First Name</label><input type="text" name="first_name" required></div>
                <div><label>Middle Name</label><input type="text" name="middle_name" required></div>
                <div><label>Suffix</label><input type="text" name="suffix"></div>
                <div><label>Birthdate</label><input type="date" name="birthdate" required></div>
                <div><label>Birthplace</label><input type="text" name="birthplace" required></div>
                <div><label>Religion</label><input type="text" name="religion" required></div>
                <div><label>Civil Status</label><input type="text" name="civil_status" required></div>
                <div><label>Sex</label>
                    <select name="sex" required><option>Male</option><option>Female</option></select>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="grid">
                <div><label>Mobile Number</label><input type="text" name="mobile" required></div>
                <div><label>Email Address</label><input type="email" name="email" required></div>
            </div>
        </div>

        <div class="section">
            <div class="grid">
                <div class="full"><strong>Current Address</strong></div>
                <div><label>House No. & Street</label><input id="curr_house_street" type="text" name="curr_house_street" required></div>
                <div><label>Barangay</label><input id="curr_barangay" type="text" name="curr_barangay" required></div>
                <div><label>City/Municipality</label><input id="curr_city" type="text" name="curr_city" required></div>
                <div><label>Province</label><input id="curr_province" type="text" name="curr_province" required></div>
                <div><label>ZIP Code</label><input id="curr_zip" type="text" name="curr_zip" required></div>
                <div class="full"><strong>Permanent Address</strong> <label><input type="checkbox" onchange="sameAsCurrent(this)"> Same as Current Address</label></div>
                <div><label>House No. & Street</label><input id="perm_house_street" type="text" name="perm_house_street" required></div>
                <div><label>Barangay</label><input id="perm_barangay" type="text" name="perm_barangay" required></div>
                <div><label>City/Municipality</label><input id="perm_city" type="text" name="perm_city" required></div>
                <div><label>Province</label><input id="perm_province" type="text" name="perm_province" required></div>
                <div><label>ZIP Code</label><input id="perm_zip" type="text" name="perm_zip" required></div>
            </div>
        </div>

        <div class="section">
            <div class="grid">
                <div class="full"><strong>Educational Background</strong></div>
                <div class="full"><label><input type="checkbox" onchange="document.getElementById('elem_group').style.display=this.checked?'grid':'none'"> Elementary School</label></div>
                <div id="elem_group" class="grid" style="display:none">
                    <div><label>Name</label><input type="text" name="elem_name"></div>
                    <div><label>Address</label><input type="text" name="elem_address"></div>
                    <div><label>Year Graduated</label><input type="text" name="elem_year_graduated"></div>
                </div>
                <div class="full"><label>Last School Attended</label></div>
                <div><label>Name</label><input type="text" name="last_school_name"></div>
                <div><label>Address</label><input type="text" name="last_school_address"></div>
                <div class="full"><label><input type="checkbox" onchange="document.getElementById('jhs_group').style.display=this.checked?'grid':'none'"> Junior High School</label></div>
                <div id="jhs_group" class="grid" style="display:none">
                    <div><label>Name</label><input type="text" name="jhs_name"></div>
                    <div><label>Address</label><input type="text" name="jhs_address"></div>
                    <div><label>Year Graduated</label><input type="text" name="jhs_year_graduated"></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="grid">
                <div class="full"><strong>Academic Information</strong></div>
                <div class="full"><label>LRN</label><input type="text" name="lrn" required></div>
            </div>
        </div>

        <div class="section">
            <div class="grid">
                <div class="full"><strong>Guardian Information</strong></div>
                <div><label>Last Name</label><input type="text" name="guardian_last_name" required></div>
                <div><label>First Name</label><input type="text" name="guardian_first_name" required></div>
                <div><label>Middle Name</label><input type="text" name="guardian_middle_name" required></div>
                <div><label>Contact Number</label><input type="text" name="guardian_contact" required></div>
                <div><label>Occupation</label><input type="text" name="guardian_occupation" required></div>
                <div class="full"><label>Address</label><input type="text" name="guardian_address" required></div>
                <div class="full"><label>Relationship</label><input type="text" name="guardian_relationship" required></div>
            </div>
        </div>

        <div class="section">
            <div class="grid">
                <div class="full"><strong>Parents Information</strong></div>
                <div><label>Mother Last Name</label><input type="text" name="mother_last_name" required></div>
                <div><label>Mother First Name</label><input type="text" name="mother_first_name" required></div>
                <div><label>Mother Middle Name</label><input type="text" name="mother_middle_name" required></div>
                <div><label>Mother Contact</label><input type="text" name="mother_contact" required></div>
                <div><label>Mother Occupation</label><input type="text" name="mother_occupation" required></div>
                <div class="full"><label>Mother Address</label><input type="text" name="mother_address" required></div>
                <div><label>Father Last Name</label><input type="text" name="father_last_name" required></div>
                <div><label>Father First Name</label><input type="text" name="father_first_name" required></div>
                <div><label>Father Middle Name</label><input type="text" name="father_middle_name" required></div>
                <div><label>Father Contact</label><input type="text" name="father_contact" required></div>
                <div><label>Father Occupation</label><input type="text" name="father_occupation" required></div>
                <div class="full"><label>Father Address</label><input type="text" name="father_address" required></div>
            </div>
        </div>

        <div class="section">
            <button name="submit_app">Submit Application</button>
        </div>
    </form>
</div>
</body>
</html>

