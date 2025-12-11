<?php
$pdo = null;
$error = '';

try {
    // Note: Assuming pdo_functions.php includes the definition for the pdoCRUD class
    include "pdo_functions.php";
    $pdo = new pdoCRUD();
} catch (Exception $e) {
    // In case of PDO/DB connection failure
    $error = 'Database connection error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}

if(isset($_POST['submit_app']) && $pdo){
    $req = ['department','grade_level','student_type','family_name','first_name','middle_name','birthdate','birthplace','religion','civil_status','sex','mobile','email','curr_house_street','curr_barangay','curr_city','curr_province','curr_zip','perm_house_street','perm_barangay','perm_city','perm_province','perm_zip','lrn','guardian_last_name','guardian_first_name','guardian_middle_name','guardian_contact','guardian_occupation','guardian_address','guardian_relationship','mother_last_name','mother_first_name','mother_middle_name','mother_contact','mother_occupation','mother_address','father_last_name','father_first_name','father_middle_name','father_contact','father_occupation','father_address'];
    
    // Input validation for required fields
    foreach($req as $k){ 
        // Use isset and check against empty string and 0 for better robustness, though empty() handles most
        if(empty($_POST[$k])){ 
            $error = "Please complete all required fields"; 
            break; 
        } 
    }

    if($error === ''){
        // Data sanitation and preparation for database insertion using prepared statements
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
        
        $id = $pdo->insertApplicant($data); // Assuming this returns a truthy value on success
        
        if($id){
            // --- START OF MODIFICATION ---
            $lrn = $data[':lrn'];
            // Generate the login ID string as requested: S-LRN + @domain. The LRN field is used, assuming it's a 12-digit number.
            // Using a simple example format here, which is more robust than relying on an auto-incremented ID being exactly '0000001'
            $login_prefix = preg_replace('/[^0-9]/', '', $lrn); // Clean LRN to be just digits
            if (strlen($login_prefix) > 7) {
                 // Use last 7 digits if LRN is full 12 digits, or the full cleaned LRN if shorter, but should be LRN.
                 $login_prefix = $login_prefix;
            }
            $login_id = "S-" . htmlspecialchars($login_prefix, ENT_QUOTES, 'UTF-8') . "@evelio.ams.edu";
            
            // Craft the message to include the login ID
            $message = "Application submitted successfully! Please note your login ID for future access: " . $login_id . ". We will review your application and contact you soon.";
            
            // Display the enhanced notification before redirecting
            echo "<script>alert('{$message}');window.location.href='index.php';</script>";
            // --- END OF MODIFICATION ---
            
            exit;
        }else{
            $error = "Unable to submit application. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>Student Application Form - Evelio AMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <style>
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: var(--spacing-8);
            padding: 0 var(--spacing-4);
            overflow-x: auto;
        }
        .step-indicator-inner {
            display: flex;
            align-items: center;
            gap: var(--spacing-1);
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-width: 80px;
        }
        .step-number {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: var(--font-weight-semibold);
            font-size: var(--font-size-sm);
            background-color: var(--color-gray-200);
            color: var(--color-text-muted);
            border: 2px solid var(--color-gray-200);
            transition: all var(--transition-normal);
            position: relative;
            z-index: 1;
        }
        .step.active .step-number {
            background-color: var(--color-accent);
            color: var(--color-white);
            border-color: var(--color-accent);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }
        .step.completed .step-number {
            background-color: var(--color-success);
            color: var(--color-white);
            border-color: var(--color-success);
        }
        .step.completed .step-number::after {
            content: '';
            position: absolute;
            width: 10px;
            height: 6px;
            border-left: 2px solid white;
            border-bottom: 2px solid white;
            transform: rotate(-45deg);
            top: 50%;
            left: 50%;
            margin-left: -5px;
            margin-top: -4px;
        }
        .step.completed .step-number span {
            display: none;
        }
        .step-label {
            font-size: var(--font-size-xs);
            color: var(--color-text-muted);
            margin-top: var(--spacing-2);
            text-align: center;
            white-space: nowrap;
            transition: color var(--transition-normal);
        }
        .step.active .step-label {
            color: var(--color-accent);
            font-weight: var(--font-weight-medium);
        }
        .step.completed .step-label {
            color: var(--color-success);
        }
        .step-connector {
            width: 30px;
            height: 2px;
            background-color: var(--color-gray-200);
            margin-bottom: 24px;
            transition: background-color var(--transition-normal);
        }
        .step.completed + .step-connector {
            background-color: var(--color-success);
        }
        .form-step {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .form-step.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .step-title {
            text-align: center;
            margin-bottom: var(--spacing-6);
        }
        .step-title h2 {
            font-size: var(--font-size-xl);
            color: var(--color-text-primary);
            margin-bottom: var(--spacing-2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-2);
        }
        .step-title h2 svg {
            color: var(--color-accent);
        }
        .step-title p {
            color: var(--color-text-secondary);
            font-size: var(--font-size-sm);
        }
        .department-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--spacing-4);
            margin-bottom: var(--spacing-6);
        }
        .department-card {
            border: 2px solid var(--color-border);
            border-radius: var(--radius-xl);
            padding: var(--spacing-6);
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-fast);
            background-color: var(--color-white);
        }
        .department-card:hover {
            border-color: var(--color-accent);
            background-color: var(--color-info-light);
        }
        .department-card.selected {
            border-color: var(--color-accent);
            background-color: var(--color-info-light);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .department-card-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto var(--spacing-3);
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
            border-radius: var(--radius-lg);
        }
        .department-card-icon svg {
            width: 24px;
            height: 24px;
            color: var(--color-white);
        }
        .department-card h4 {
            margin-bottom: var(--spacing-1);
            color: var(--color-text-primary);
        }
        .department-card p {
            font-size: var(--font-size-sm);
            color: var(--color-text-secondary);
            margin-bottom: 0;
        }
        .form-navigation {
            display: flex;
            justify-content: space-between;
            gap: var(--spacing-4);
            margin-top: var(--spacing-8);
            padding-top: var(--spacing-6);
            border-top: 1px solid var(--color-border-light);
        }
        .form-navigation .btn {
            min-width: 140px;
        }
        .same-address-toggle {
            background-color: var(--color-info-light);
            border: 1px solid var(--color-accent);
            border-radius: var(--radius-lg);
            padding: var(--spacing-4);
            margin-bottom: var(--spacing-5);
        }
        .same-address-toggle .form-check {
            margin-bottom: 0;
        }
        .address-section-label {
            font-size: var(--font-size-base);
            font-weight: var(--font-weight-semibold);
            color: var(--color-text-primary);
            margin-bottom: var(--spacing-4);
            display: flex;
            align-items: center;
            gap: var(--spacing-2);
        }
        .address-section-label svg {
            color: var(--color-accent);
        }
        .review-section {
            background-color: var(--color-gray-50);
            border-radius: var(--radius-lg);
            padding: var(--spacing-5);
            margin-bottom: var(--spacing-4);
        }
        .review-section h4 {
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-semibold);
            color: var(--color-primary);
            margin-bottom: var(--spacing-3);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .review-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--spacing-3);
        }
        .review-item {
            font-size: var(--font-size-sm);
        }
        .review-item-label {
            color: var(--color-text-muted);
            margin-bottom: 2px;
        }
        .review-item-value {
            color: var(--color-text-primary);
            font-weight: var(--font-weight-medium);
        }
        .strand-field {
            display: none;
        }
        .strand-field.show {
            display: block;
        }
        .education-toggle {
            background-color: var(--color-gray-50);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: var(--spacing-4);
            margin-bottom: var(--spacing-4);
        }
        .education-toggle .form-check {
            margin-bottom: 0;
        }
        .education-fields {
            display: none;
            margin-top: var(--spacing-4);
            padding-top: var(--spacing-4);
            border-top: 1px solid var(--color-border);
        }
        .education-fields.show {
            display: block;
        }
        .progress-bar-container {
            background-color: var(--color-gray-200);
            border-radius: var(--radius-full);
            height: 4px;
            margin-bottom: var(--spacing-6);
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--color-accent) 0%, var(--color-success) 100%);
            border-radius: var(--radius-full);
            transition: width var(--transition-normal);
        }
        @media (max-width: 768px) {
            .step-indicator {
                padding: 0;
            }
            .step {
                min-width: 50px;
            }
            .step-label {
                display: none;
            }
            .step-connector {
                width: 15px;
                margin-bottom: 0;
            }
            .department-cards {
                grid-template-columns: 1fr;
            }
            .review-grid {
                grid-template-columns: 1fr;
            }
            .form-navigation {
                flex-direction: column-reverse;
            }
            .form-navigation .btn {
                width: 100%;
            }
        }
        .success-animation {
            text-align: center;
            padding: var(--spacing-8);
        }
        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto var(--spacing-4);
            background-color: var(--color-success-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-icon svg {
            width: 40px;
            height: 40px;
            color: var(--color-success);
        }
    </style>
</head>
<body>
    <div class="registration-layout">
        <div class="registration-container">
            <div class="registration-header">
                <div class="registration-logo">
                    <div class="registration-logo-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <span class="registration-logo-text">Evelio AMS</span>
                </div>
                <h1 class="registration-title">Student Application Form</h1>
                <p class="registration-subtitle">Complete the form below to apply for enrollment</p>
            </div>

            <div class="registration-card">
                <div class="registration-card-body">
                    <?php if($error !== ''): ?>
                    <div class="alert alert-danger mb-6">
                        <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div class="alert-content"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <?php endif; ?>

                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progressBar" style="width: 11.11%"></div>
                    </div>

                    <div class="step-indicator">
                        <div class="step-indicator-inner">
                            <div class="step active" data-step="1">
                                <div class="step-number"><span>1</span></div>
                                <div class="step-label">Department</div>
                            </div>
                            <div class="step-connector"></div>
                            <div class="step" data-step="2">
                                <div class="step-number"><span>2</span></div>
                                <div class="step-label">Personal</div>
                            </div>
                            <div class="step-connector"></div>
                            <div class="step" data-step="3">
                                <div class="step-number"><span>3</span></div>
                                <div class="step-label">Contact</div>
                            </div>
                            <div class="step-connector"></div>
                            <div class="step" data-step="4">
                                <div class="step-number"><span>4</span></div>
                                <div class="step-label">Address</div>
                            </div>
                            <div class="step-connector"></div>
                            <div class="step" data-step="5">
                                <div class="step-number"><span>5</span></div>
                                <div class="step-label">Education</div>
                            </div>
                            <div class="step-connector"></div>
                            <div class="step" data-step="6">
                                <div class="step-number"><span>6</span></div>
                                <div class="step-label">Academic</div>
                            </div>
                            <div class="step-connector"></div>
                            <div class="step" data-step="7">
                                <div class="step-number"><span>7</span></div>
                                <div class="step-label">Guardian</div>
                            </div>
                            <div class="step-connector"></div>
                            <div class="step" data-step="8">
                                <div class="step-number"><span>8</span></div>
                                <div class="step-label">Parents</div>
                            </div>
                            <div class="step-connector"></div>
                            <div class="step" data-step="9">
                                <div class="step-number"><span>9</span></div>
                                <div class="step-label">Review</div>
                            </div>
                        </div>
                    </div>

                    <form method="post" id="applicationForm">
                        <div class="form-step active" data-step="1">
                            <div class="step-title">
                                <h2>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                    </svg>
                                    Department Selection
                                </h2>
                                <p>Choose your department and grade level</p>
                            </div>

                            <div class="department-cards">
                                <div class="department-card" data-department="JHS">
                                    <div class="department-card-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                        </svg>
                                    </div>
                                    <h4>Junior High School</h4>
                                    <p>Grades 7-10</p>
                                </div>
                                <div class="department-card" data-department="SHS">
                                    <div class="department-card-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                        </svg>
                                    </div>
                                    <h4>Senior High School</h4>
                                    <p>Grades 11-12</p>
                                </div>
                            </div>

                            <input type="hidden" name="department" id="department" required>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label required">Grade Level</label>
                                    <select name="grade_level" id="grade_level" class="form-control" required>
                                        <option value="">Select Grade Level</option>
                                        <option value="Grade 7">Grade 7</option>
                                        <option value="Grade 8">Grade 8</option>
                                        <option value="Grade 9">Grade 9</option>
                                        <option value="Grade 10">Grade 10</option>
                                        <option value="Grade 11">Grade 11</option>
                                        <option value="Grade 12">Grade 12</option>
                                    </select>
                                </div>
                                <div class="form-group strand-field" id="strandField">
                                    <label class="form-label">Strand</label>
                                    <select name="strand" id="strand" class="form-control">
                                        <option value="">Select Strand</option>
                                        <option value="HUMSS">HUMSS</option>
                                        <option value="TVL">TVL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Student Type</label>
                                <select name="student_type" id="student_type" class="form-control" required>
                                    <option value="">Select Student Type</option>
                                    <option value="New Student">New Student</option>
                                    <option value="Old Student">Old Student</option>
                                    <option value="Transferee">Transferee</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-step" data-step="2">
                            <div class="step-title">
                                <h2>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    Personal Information
                                </h2>
                                <p>Enter your personal details</p>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label required">Family Name</label>
                                    <input type="text" name="family_name" id="family_name" class="form-control" placeholder="Enter family name" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">First Name</label>
                                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter first name" required>
                                </div>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label required">Middle Name</label>
                                    <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Enter middle name" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Suffix</label>
                                    <input type="text" name="suffix" id="suffix" class="form-control" placeholder="e.g., Jr., III">
                                </div>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label required">Date of Birth</label>
                                    <input type="date" name="birthdate" id="birthdate" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Place of Birth</label>
                                    <input type="text" name="birthplace" id="birthplace" class="form-control" placeholder="City/Municipality, Province" required>
                                </div>
                            </div>

                            <div class="form-row-3">
                                <div class="form-group">
                                    <label class="form-label required">Religion</label>
                                    <input type="text" name="religion" id="religion" class="form-control" placeholder="Enter religion" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Civil Status</label>
                                    <select name="civil_status" id="civil_status" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Sex</label>
                                    <select name="sex" id="sex" class="form-control" required>
                                        <option value="">Select Sex</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-step" data-step="3">
                            <div class="step-title">
                                <h2>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    Contact Information
                                </h2>
                                <p>How can we reach you?</p>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label required">Mobile Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+63</span>
                                        <input type="tel" name="mobile" id="mobile" class="form-control" placeholder="9XX XXX XXXX" required>
                                    </div>
                                    <div class="form-text">Enter your 10-digit mobile number</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Email Address</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="your.email@example.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-step" data-step="4">
                            <div class="step-title">
                                <h2>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    Address Information
                                </h2>
                                <p>Enter your current and permanent address</p>
                            </div>

                            <div class="address-section-label">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                                Current Address
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label required">House No. & Street</label>
                                    <input type="text" name="curr_house_street" id="curr_house_street" class="form-control" placeholder="House No. & Street" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Barangay</label>
                                    <input type="text" name="curr_barangay" id="curr_barangay" class="form-control" placeholder="Barangay" required>
                                </div>
                            </div>

                            <div class="form-row-3">
                                <div class="form-group">
                                    <label class="form-label required">City/Municipality</label>
                                    <input type="text" name="curr_city" id="curr_city" class="form-control" placeholder="City/Municipality" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Province</label>
                                    <input type="text" name="curr_province" id="curr_province" class="form-control" placeholder="Province" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">ZIP Code</label>
                                    <input type="text" name="curr_zip" id="curr_zip" class="form-control" placeholder="ZIP Code" required>
                                </div>
                            </div>

                            <div class="same-address-toggle">
                                <div class="form-check">
                                    <input type="checkbox" id="sameAddress" class="form-check-input">
                                    <label for="sameAddress" class="form-check-label">Permanent address is the same as current address</label>
                                </div>
                            </div>

                            <div class="address-section-label">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                Permanent Address
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label required">House No. & Street</label>
                                    <input type="text" name="perm_house_street" id="perm_house_street" class="form-control" placeholder="House No. & Street" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Barangay</label>
                                    <input type="text" name="perm_barangay" id="perm_barangay" class="form-control" placeholder="Barangay" required>
                                </div>
                            </div>

                            <div class="form-row-3">
                                <div class="form-group">
                                    <label class="form-label required">City/Municipality</label>
                                    <input type="text" name="perm_city" id="perm_city" class="form-control" placeholder="City/Municipality" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Province</label>
                                    <input type="text" name="perm_province" id="perm_province" class="form-control" placeholder="Province" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">ZIP Code</label>
                                    <input type="text" name="perm_zip" id="perm_zip" class="form-control" placeholder="ZIP Code" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-step" data-step="5">
                            <div class="step-title">
                                <h2>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    </svg>
                                    Educational Background
                                </h2>
                                <p>Enter your previous school information</p>
                            </div>

                            <div class="education-toggle" id="elemToggle">
                                <div class="form-check">
                                    <input type="checkbox" id="showElem" class="form-check-input">
                                    <label for="showElem" class="form-check-label">Include Elementary School Information</label>
                                </div>
                                <div class="education-fields" id="elemFields">
                                    <div class="form-row-2">
                                        <div class="form-group">
                                            <label class="form-label">School Name</label>
                                            <input type="text" name="elem_name" id="elem_name" class="form-control" placeholder="Elementary school name">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">School Address</label>
                                            <input type="text" name="elem_address" id="elem_address" class="form-control" placeholder="School address">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Year Graduated</label>
                                        <input type="text" name="elem_year_graduated" id="elem_year_graduated" class="form-control" placeholder="e.g., 2020">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                    </svg>
                                    Last School Attended
                                </div>
                                <div class="form-row-2">
                                    <div class="form-group">
                                        <label class="form-label">School Name</label>
                                        <input type="text" name="last_school_name" id="last_school_name" class="form-control" placeholder="Last school name">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">School Address</label>
                                        <input type="text" name="last_school_address" id="last_school_address" class="form-control" placeholder="School address">
                                    </div>
                                </div>
                            </div>

                            <div class="education-toggle" id="jhsToggle">
                                <div class="form-check">
                                    <input type="checkbox" id="showJhs" class="form-check-input">
                                    <label for="showJhs" class="form-check-label">Include Junior High School Information</label>
                                </div>
                                <div class="education-fields" id="jhsFields">
                                    <div class="form-row-2">
                                        <div class="form-group">
                                            <label class="form-label">School Name</label>
                                            <input type="text" name="jhs_name" id="jhs_name" class="form-control" placeholder="JHS name">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">School Address</label>
                                            <input type="text" name="jhs_address" id="jhs_address" class="form-control" placeholder="School address">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Year Graduated</label>
                                        <input type="text" name="jhs_year_graduated" id="jhs_year_graduated" class="form-control" placeholder="e.g., 2024">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-step" data-step="6">
                            <div class="step-title">
                                <h2>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    Academic Information
                                </h2>
                                <p>Enter your learner reference number</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Learner Reference Number (LRN)</label>
                                <input type="text" name="lrn" id="lrn" class="form-control form-control-lg" placeholder="Enter your 12-digit LRN" maxlength="12" required>
                                <div class="form-text">Your LRN is a 12-digit number assigned by DepEd. You can find this on your report card or school records.</div>
                            </div>
                        </div>

                        <div class="form-step" data-step="7">
                            <div class="step-title">
                                <h2>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    Guardian Information
                                </h2>
                                <p>Enter your guardian's details</p>
                            </div>

                            <div class="form-row-3">
                                <div class="form-group">
                                    <label class="form-label required">Last Name</label>
                                    <input type="text" name="guardian_last_name" id="guardian_last_name" class="form-control" placeholder="Last name" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">First Name</label>
                                    <input type="text" name="guardian_first_name" id="guardian_first_name" class="form-control" placeholder="First name" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Middle Name</label>
                                    <input type="text" name="guardian_middle_name" id="guardian_middle_name" class="form-control" placeholder="Middle name" required>
                                </div>
                            </div>

                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label required">Contact Number</label>
                                    <input type="tel" name="guardian_contact" id="guardian_contact" class="form-control" placeholder="Contact number" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Occupation</label>
                                    <input type="text" name="guardian_occupation" id="guardian_occupation" class="form-control" placeholder="Occupation" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Address</label>
                                <input type="text" name="guardian_address" id="guardian_address" class="form-control" placeholder="Complete address" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Relationship to Student</label>
                                <select name="guardian_relationship" id="guardian_relationship" class="form-control" required>
                                    <option value="">Select Relationship</option>
                                    <option value="Parent">Parent</option>
                                    <option value="Grandparent">Grandparent</option>
                                    <option value="Sibling">Sibling</option>
                                    <option value="Uncle/Aunt">Uncle/Aunt</option>
                                    <option value="Other Relative">Other Relative</option>
                                    <option value="Legal Guardian">Legal Guardian</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-step" data-step="8">
                            <div class="step-title">
                                <h2>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    Parent Information
                                </h2>
                                <p>Enter your parents' details</p>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="7" r="4"/>
                                        <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                                    </svg>
                                    Mother's Information
                                </div>
                                <div class="form-row-3">
                                    <div class="form-group">
                                        <label class="form-label required">Last Name</label>
                                        <input type="text" name="mother_last_name" id="mother_last_name" class="form-control" placeholder="Last name" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required">First Name</label>
                                        <input type="text" name="mother_first_name" id="mother_first_name" class="form-control" placeholder="First name" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required">Middle Name</label>
                                        <input type="text" name="mother_middle_name" id="mother_middle_name" class="form-control" placeholder="Middle name" required>
                                    </div>
                                </div>
                                <div class="form-row-2">
                                    <div class="form-group">
                                        <label class="form-label required">Contact Number</label>
                                        <input type="tel" name="mother_contact" id="mother_contact" class="form-control" placeholder="Contact number" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required">Occupation</label>
                                        <input type="text" name="mother_occupation" id="mother_occupation" class="form-control" placeholder="Occupation" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Address</label>
                                    <input type="text" name="mother_address" id="mother_address" class="form-control" placeholder="Complete address" required>
                                </div>
                            </div>

                            <div class="form-section-divider"></div>

                            <div class="form-section">
                                <div class="form-section-title">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="7" r="4"/>
                                        <path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
                                    </svg>
                                    Father's Information
                                </div>
                                <div class="form-row-3">
                                    <div class="form-group">
                                        <label class="form-label required">Last Name</label>
                                        <input type="text" name="father_last_name" id="father_last_name" class="form-control" placeholder="Last name" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required">First Name</label>
                                        <input type="text" name="father_first_name" id="father_first_name" class="form-control" placeholder="First name" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required">Middle Name</label>
                                        <input type="text" name="father_middle_name" id="father_middle_name" class="form-control" placeholder="Middle name" required>
                                    </div>
                                </div>
                                <div class="form-row-2">
                                    <div class="form-group">
                                        <label class="form-label required">Contact Number</label>
                                        <input type="tel" name="father_contact" id="father_contact" class="form-control" placeholder="Contact number" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label required">Occupation</label>
                                        <input type="text" name="father_occupation" id="father_occupation" class="form-control" placeholder="Occupation" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required">Address</label>
                                    <input type="text" name="father_address" id="father_address" class="form-control" placeholder="Complete address" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-step" data-step="9">
                            <div class="step-title">
                                <h2>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                    Review & Submit
                                </h2>
                                <p>Please review your information before submitting</p>
                            </div>

                            <div class="review-section">
                                <h4>Department & Academic</h4>
                                <div class="review-grid">
                                    <div class="review-item">
                                        <div class="review-item-label">Department</div>
                                        <div class="review-item-value" id="review-department">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Grade Level</div>
                                        <div class="review-item-value" id="review-grade_level">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Strand</div>
                                        <div class="review-item-value" id="review-strand">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Student Type</div>
                                        <div class="review-item-value" id="review-student_type">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">LRN</div>
                                        <div class="review-item-value" id="review-lrn">-</div>
                                    </div>
                                </div>
                            </div>

                            <div class="review-section">
                                <h4>Personal Information</h4>
                                <div class="review-grid">
                                    <div class="review-item">
                                        <div class="review-item-label">Full Name</div>
                                        <div class="review-item-value" id="review-fullname">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Date of Birth</div>
                                        <div class="review-item-value" id="review-birthdate">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Place of Birth</div>
                                        <div class="review-item-value" id="review-birthplace">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Sex</div>
                                        <div class="review-item-value" id="review-sex">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Religion</div>
                                        <div class="review-item-value" id="review-religion">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Civil Status</div>
                                        <div class="review-item-value" id="review-civil_status">-</div>
                                    </div>
                                </div>
                            </div>

                            <div class="review-section">
                                <h4>Contact Information</h4>
                                <div class="review-grid">
                                    <div class="review-item">
                                        <div class="review-item-label">Mobile Number</div>
                                        <div class="review-item-value" id="review-mobile">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Email Address</div>
                                        <div class="review-item-value" id="review-email">-</div>
                                    </div>
                                </div>
                            </div>

                            <div class="review-section">
                                <h4>Address</h4>
                                <div class="review-grid">
                                    <div class="review-item">
                                        <div class="review-item-label">Current Address</div>
                                        <div class="review-item-value" id="review-current_address">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Permanent Address</div>
                                        <div class="review-item-value" id="review-permanent_address">-</div>
                                    </div>
                                </div>
                            </div>

                            <div class="review-section">
                                <h4>Guardian Information</h4>
                                <div class="review-grid">
                                    <div class="review-item">
                                        <div class="review-item-label">Guardian Name</div>
                                        <div class="review-item-value" id="review-guardian_name">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Contact Number</div>
                                        <div class="review-item-value" id="review-guardian_contact">-</div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-item-label">Relationship</div>
                                        <div class="review-item-value" id="review-guardian_relationship">-</div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-6">
                                <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="16" x2="12" y2="12"/>
                                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                <div class="alert-content">
                                    <div class="alert-title">Please verify your information</div>
                                    Make sure all information is correct before submitting. Once submitted, your application will be reviewed by our admissions team.
                                </div>
                            </div>
                        </div>

                        <div class="form-navigation">
                            <button type="button" id="prevBtn" class="btn btn-secondary btn-lg" style="display: none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="19" y1="12" x2="5" y2="12"/>
                                    <polyline points="12 19 5 12 12 5"/>
                                </svg>
                                Previous
                            </button>
                            <a href="apply_consent.php" id="backToConsent" class="btn btn-secondary btn-lg">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="19" y1="12" x2="5" y2="12"/>
                                    <polyline points="12 19 5 12 12 5"/>
                                </svg>
                                Back
                            </a>
                            <button type="button" id="nextBtn" class="btn btn-primary btn-lg">
                                Next Step
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <polyline points="12 5 19 12 12 19"/>
                                </svg>
                            </button>
                            <button type="submit" name="submit_app" id="submitBtn" class="btn btn-success btn-lg" style="display: none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-6">
                </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const totalSteps = 9;
            const form = document.getElementById('applicationForm');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const backToConsent = document.getElementById('backToConsent');
            const progressBar = document.getElementById('progressBar');

            const departmentCards = document.querySelectorAll('.department-card');
            departmentCards.forEach(card => {
                card.addEventListener('click', function() {
                    departmentCards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('department').value = this.dataset.department;
                    updateGradeLevels(this.dataset.department);
                    updateStrandVisibility(this.dataset.department);
                });
            });

            function updateGradeLevels(department) {
                const gradeSelect = document.getElementById('grade_level');
                const selectedGrade = gradeSelect.value;
                gradeSelect.innerHTML = '<option value="">Select Grade Level</option>';
                
                let grades = [];
                if (department === 'JHS') {
                    grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
                } else if (department === 'SHS') {
                    grades = ['Grade 11', 'Grade 12'];
                }

                grades.forEach(grade => {
                    const option = document.createElement('option');
                    option.value = grade;
                    option.textContent = grade;
                    if (grade === selectedGrade) {
                        option.selected = true; // Keep selected value if it's still available
                    }
                    gradeSelect.appendChild(option);
                });
            }

            function updateStrandVisibility(department) {
                const strandField = document.getElementById('strandField');
                if (department === 'SHS') {
                    strandField.classList.add('show');
                } else {
                    strandField.classList.remove('show');
                    document.getElementById('strand').value = '';
                }
            }

            document.getElementById('sameAddress').addEventListener('change', function() {
                const isChecked = this.checked;
                const currFields = ['curr_house_street', 'curr_barangay', 'curr_city', 'curr_province', 'curr_zip'];
                const permFields = ['perm_house_street', 'perm_barangay', 'perm_city', 'perm_province', 'perm_zip'];

                if (isChecked) {
                    for (let i = 0; i < currFields.length; i++) {
                        const curr = document.getElementById(currFields[i]);
                        const perm = document.getElementById(permFields[i]);
                        perm.value = curr.value;
                        perm.readOnly = true; // Make field read-only to show they are linked
                    }
                } else {
                    for (let i = 0; i < permFields.length; i++) {
                        document.getElementById(permFields[i]).readOnly = false; // Remove read-only
                        document.getElementById(permFields[i]).value = ''; // Clear for separate input
                    }
                }
            });

            // Added listeners to current address fields to automatically update permanent address if checkbox is checked
            document.querySelectorAll('#curr_house_street, #curr_barangay, #curr_city, #curr_province, #curr_zip').forEach(field => {
                field.addEventListener('input', function() {
                    if (document.getElementById('sameAddress').checked) {
                        // Manually trigger the copy logic to update linked fields
                        const mapping = {
                            'curr_house_street': 'perm_house_street',
                            'curr_barangay': 'perm_barangay',
                            'curr_city': 'perm_city',
                            'curr_province': 'perm_province',
                            'curr_zip': 'perm_zip'
                        };
                        document.getElementById(mapping[this.id]).value = this.value;
                    }
                });
            });


            document.getElementById('showElem').addEventListener('change', function() {
                document.getElementById('elemFields').classList.toggle('show', this.checked);
            });

            document.getElementById('showJhs').addEventListener('change', function() {
                document.getElementById('jhsFields').classList.toggle('show', this.checked);
            });

            function showStep(step) {
                document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
                document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');

                document.querySelectorAll('.step').forEach(s => {
                    const stepNum = parseInt(s.dataset.step);
                    s.classList.remove('active', 'completed');
                    if (stepNum === step) {
                        s.classList.add('active');
                    } else if (stepNum < step) {
                        s.classList.add('completed');
                    }
                });

                progressBar.style.width = ((step / totalSteps) * 100) + '%';

                prevBtn.style.display = step > 1 ? 'inline-flex' : 'none';
                backToConsent.style.display = step === 1 ? 'inline-flex' : 'none';
                nextBtn.style.display = step < totalSteps ? 'inline-flex' : 'none';
                submitBtn.style.display = step === totalSteps ? 'inline-flex' : 'none';

                // Adjust navigation buttons display on step 1
                if (step === 1) {
                     prevBtn.style.display = 'none'; // Ensure back button is hidden on step 1
                     backToConsent.style.display = 'inline-flex'; // Show link to consent page
                } else {
                     backToConsent.style.display = 'none';
                }


                if (step === totalSteps) {
                    populateReview();
                }
            }

            function validateStep(step) {
                const stepEl = document.querySelector(`.form-step[data-step="${step}"]`);
                const requiredFields = stepEl.querySelectorAll('[required]');
                let isValid = true;

                // Simple client-side validation for required fields
                requiredFields.forEach(field => {
                    field.classList.remove('is-invalid');
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });

                if (step === 1 && !document.getElementById('department').value) {
                    // Highlight cards or provide a better visual cue for Step 1 selection
                    if (!document.querySelector('.department-card.selected')) {
                        alert('Please select a department and grade level.');
                        isValid = false;
                    }
                }
                
                if (step === 3) {
                    // Mobile number simple validation (e.g., must be 10 digits for the 9XX XXX XXXX format)
                    const mobile = document.getElementById('mobile').value.trim();
                    if (mobile.length !== 10 || !/^\d{10}$/.test(mobile)) {
                        document.getElementById('mobile').classList.add('is-invalid');
                        alert('Please enter a valid 10-digit mobile number (e.g., 9171234567).');
                        isValid = false;
                    }
                    // Email validation (just check browser validation if input type="email" is used, otherwise add pattern check)
                    const email = document.getElementById('email');
                    if (!email.checkValidity()) {
                        email.classList.add('is-invalid');
                        alert('Please enter a valid email address.');
                        isValid = false;
                    }
                }

                if (!isValid) {
                     // Scroll to the first invalid field
                     const firstInvalid = stepEl.querySelector('.is-invalid');
                     if (firstInvalid) {
                        firstInvalid.focus();
                     }
                }

                return isValid;
            }

            function populateReview() {
                const getValue = id => document.getElementById(id)?.value.trim() || '-';
                
                // Format the address for display
                const formatAddress = (prefix) => {
                    const parts = [
                        getValue(`${prefix}_house_street`), 
                        getValue(`${prefix}_barangay`), 
                        getValue(`${prefix}_city`), 
                        getValue(`${prefix}_province`),
                        getValue(`${prefix}_zip`)
                    ].filter(p => p !== '-');
                    return parts.join(', ') || '-';
                };

                document.getElementById('review-department').textContent = getValue('department');
                document.getElementById('review-grade_level').textContent = getValue('grade_level');
                document.getElementById('review-strand').textContent = getValue('strand') || 'N/A';
                document.getElementById('review-student_type').textContent = getValue('student_type');
                document.getElementById('review-lrn').textContent = getValue('lrn');
                
                const suffix = getValue('suffix') ? ' ' + getValue('suffix') : '';
                document.getElementById('review-fullname').textContent = 
                    `${getValue('family_name')}, ${getValue('first_name')} ${getValue('middle_name')}${suffix}`;
                document.getElementById('review-birthdate').textContent = getValue('birthdate');
                document.getElementById('review-birthplace').textContent = getValue('birthplace');
                document.getElementById('review-sex').textContent = getValue('sex');
                document.getElementById('review-religion').textContent = getValue('religion');
                document.getElementById('review-civil_status').textContent = getValue('civil_status');
                
                document.getElementById('review-mobile').textContent = "+63" + getValue('mobile');
                document.getElementById('review-email').textContent = getValue('email');
                
                document.getElementById('review-current_address').textContent = formatAddress('curr');
                document.getElementById('review-permanent_address').textContent = formatAddress('perm');
                
                document.getElementById('review-guardian_name').textContent = 
                    `${getValue('guardian_first_name')} ${getValue('guardian_middle_name')} ${getValue('guardian_last_name')}`;
                document.getElementById('review-guardian_contact').textContent = getValue('guardian_contact');
                document.getElementById('review-guardian_relationship').textContent = getValue('guardian_relationship');
            }

            nextBtn.addEventListener('click', function() {
                if (validateStep(currentStep)) {
                    currentStep++;
                    showStep(currentStep);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });

            prevBtn.addEventListener('click', function() {
                currentStep--;
                showStep(currentStep);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            showStep(1);
        });
    </script>
</body>
</html>