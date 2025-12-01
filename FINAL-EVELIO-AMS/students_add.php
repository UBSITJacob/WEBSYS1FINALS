<?php
include "pdo_functions.php";
include "session.php";
require_login();
if($_SESSION['role']!=='admin'){ header('Location: index.php'); exit; }
$pdo = new pdoCRUD();
if(isset($_POST['submit_student'])){
    $req = ['department','grade_level','student_type','family_name','first_name','middle_name','birthdate','birthplace','religion','civil_status','sex','mobile','email','curr_house_street','curr_barangay','curr_city','curr_province','curr_zip','perm_house_street','perm_barangay','perm_city','perm_province','perm_zip','lrn','guardian_last_name','guardian_first_name','guardian_middle_name','guardian_contact','guardian_occupation','guardian_address','guardian_relationship','mother_last_name','mother_first_name','mother_middle_name','mother_contact','mother_occupation','mother_address','father_last_name','father_first_name','father_middle_name','father_contact','father_occupation','father_address'];
    foreach($req as $k){ if(empty($_POST[$k])){ $error = "Please complete all required fields"; break; } }
    if(!isset($error)){
        $data = [
            ':lrn'=>trim($_POST['lrn']),
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
        $id = $pdo->insertStudent($data);
        if($id){ echo "<script>alert('Student added successfully');window.location.href='students.php';</script>"; exit; } else { $error = "Unable to add student"; }
    }
}

$page_title = 'Add Student';
$breadcrumb = [
    ['title' => 'Students', 'url' => 'students.php'],
    ['title' => 'Add Student', 'active' => true]
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
                        <h1 class="page-header-title">Add Student</h1>
                        <p class="page-header-subtitle">Manually add a new student record</p>
                    </div>
                    <div class="page-header-actions">
                        <a href="students.php" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Back to Students
                        </a>
                    </div>
                </div>
            </div>
            
            <?php if(isset($error)): ?>
            <div class="alert alert-danger mb-6">
                <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div class="alert-content"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="card mb-6">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2" style="display: inline-block; vertical-align: middle;">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                            Academic Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row form-row-4">
                            <div class="form-group">
                                <label class="form-label required">Department</label>
                                <select name="department" class="form-control" required>
                                    <option value="JHS">Junior High School</option>
                                    <option value="SHS">Senior High School</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Grade Level</label>
                                <select name="grade_level" class="form-control" required>
                                    <option>Grade 7</option>
                                    <option>Grade 8</option>
                                    <option>Grade 9</option>
                                    <option>Grade 10</option>
                                    <option>Grade 11</option>
                                    <option>Grade 12</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Strand (SHS only)</label>
                                <select name="strand" class="form-control">
                                    <option value="">None</option>
                                    <option>HUMSS</option>
                                    <option>TVL</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Student Type</label>
                                <select name="student_type" class="form-control" required>
                                    <option>Old Student</option>
                                    <option>New Student</option>
                                    <option>Transferee</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">LRN (Learner Reference Number)</label>
                            <input type="text" name="lrn" class="form-control" required placeholder="Enter 12-digit LRN">
                        </div>
                    </div>
                </div>
                
                <div class="card mb-6">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2" style="display: inline-block; vertical-align: middle;">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Personal Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row form-row-4">
                            <div class="form-group">
                                <label class="form-label required">Family Name</label>
                                <input type="text" name="family_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Suffix</label>
                                <input type="text" name="suffix" class="form-control" placeholder="Jr., Sr., III, etc.">
                            </div>
                        </div>
                        <div class="form-row form-row-4">
                            <div class="form-group">
                                <label class="form-label required">Birthdate</label>
                                <input type="date" name="birthdate" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Birthplace</label>
                                <input type="text" name="birthplace" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Religion</label>
                                <input type="text" name="religion" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Civil Status</label>
                                <input type="text" name="civil_status" class="form-control" required placeholder="Single">
                            </div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">Sex</label>
                                <select name="sex" class="form-control" required>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Mobile Number</label>
                                <input type="text" name="mobile" class="form-control" required placeholder="09XXXXXXXXX">
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-6">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2" style="display: inline-block; vertical-align: middle;">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Address Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <h4 class="text-lg font-semibold mb-4">Current Address</h4>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">House No. & Street</label>
                                <input id="curr_house_street" type="text" name="curr_house_street" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Barangay</label>
                                <input id="curr_barangay" type="text" name="curr_barangay" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">City/Municipality</label>
                                <input id="curr_city" type="text" name="curr_city" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label required">Province</label>
                                <input id="curr_province" type="text" name="curr_province" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">ZIP Code</label>
                                <input id="curr_zip" type="text" name="curr_zip" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="d-flex align-center gap-3 mb-4 mt-6">
                            <h4 class="text-lg font-semibold m-0">Permanent Address</h4>
                            <label class="form-check m-0">
                                <input type="checkbox" class="form-check-input" onchange="sameAsCurrent(this)">
                                <span class="form-check-label">Same as Current Address</span>
                            </label>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">House No. & Street</label>
                                <input id="perm_house_street" type="text" name="perm_house_street" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Barangay</label>
                                <input id="perm_barangay" type="text" name="perm_barangay" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">City/Municipality</label>
                                <input id="perm_city" type="text" name="perm_city" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label required">Province</label>
                                <input id="perm_province" type="text" name="perm_province" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">ZIP Code</label>
                                <input id="perm_zip" type="text" name="perm_zip" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-6">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2" style="display: inline-block; vertical-align: middle;">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            Educational Background
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="showElem" onchange="document.getElementById('elem_group').style.display=this.checked?'block':'none'">
                            <label class="form-check-label" for="showElem">Include Elementary School Information</label>
                        </div>
                        <div id="elem_group" style="display:none" class="mb-6">
                            <div class="form-row form-row-3">
                                <div class="form-group">
                                    <label class="form-label">School Name</label>
                                    <input type="text" name="elem_name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="elem_address" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Year Graduated</label>
                                    <input type="text" name="elem_year_graduated" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="text-lg font-semibold mb-4">Last School Attended</h4>
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label">School Name</label>
                                <input type="text" name="last_school_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Address</label>
                                <input type="text" name="last_school_address" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-check mb-4 mt-6">
                            <input type="checkbox" class="form-check-input" id="showJhs" onchange="document.getElementById('jhs_group').style.display=this.checked?'block':'none'">
                            <label class="form-check-label" for="showJhs">Include Junior High School Information</label>
                        </div>
                        <div id="jhs_group" style="display:none">
                            <div class="form-row form-row-3">
                                <div class="form-group">
                                    <label class="form-label">School Name</label>
                                    <input type="text" name="jhs_name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="jhs_address" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Year Graduated</label>
                                    <input type="text" name="jhs_year_graduated" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-6">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2" style="display: inline-block; vertical-align: middle;">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Guardian Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row form-row-4">
                            <div class="form-group">
                                <label class="form-label required">Last Name</label>
                                <input type="text" name="guardian_last_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">First Name</label>
                                <input type="text" name="guardian_first_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Middle Name</label>
                                <input type="text" name="guardian_middle_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Contact Number</label>
                                <input type="text" name="guardian_contact" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">Occupation</label>
                                <input type="text" name="guardian_occupation" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Address</label>
                                <input type="text" name="guardian_address" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Relationship</label>
                                <input type="text" name="guardian_relationship" class="form-control" required placeholder="e.g., Parent, Uncle, Aunt">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-6">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2" style="display: inline-block; vertical-align: middle;">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Parents Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <h4 class="text-lg font-semibold mb-4">Mother's Information</h4>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">Last Name</label>
                                <input type="text" name="mother_last_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">First Name</label>
                                <input type="text" name="mother_first_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Middle Name</label>
                                <input type="text" name="mother_middle_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">Contact Number</label>
                                <input type="text" name="mother_contact" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Occupation</label>
                                <input type="text" name="mother_occupation" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Address</label>
                                <input type="text" name="mother_address" class="form-control" required>
                            </div>
                        </div>
                        
                        <h4 class="text-lg font-semibold mb-4 mt-6">Father's Information</h4>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">Last Name</label>
                                <input type="text" name="father_last_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">First Name</label>
                                <input type="text" name="father_first_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Middle Name</label>
                                <input type="text" name="father_middle_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row form-row-3">
                            <div class="form-group">
                                <label class="form-label required">Contact Number</label>
                                <input type="text" name="father_contact" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Occupation</label>
                                <input type="text" name="father_occupation" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Address</label>
                                <input type="text" name="father_address" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-3 justify-end">
                            <a href="students.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="submit_student" class="btn btn-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Add Student
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<script>
    function sameAsCurrent(cb) {
        if(cb.checked) {
            ['house_street', 'barangay', 'city', 'province', 'zip'].forEach(function(f) {
                document.getElementById('perm_' + f).value = document.getElementById('curr_' + f).value;
            });
        }
    }
</script>
<?php include "includes/footer.php"; ?>
