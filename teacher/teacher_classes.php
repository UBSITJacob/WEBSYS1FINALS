<?php
session_start();
if (!isset($_SESSION['teacher'])) {
    header('Location: ../index.php');
    exit;
}

require_once "teacher_functions.php";
$teacherdb = new TeacherDB();

$teacher_id = isset($_SESSION['teacher']['id']) ? (int)$_SESSION['teacher']['id'] : 0;
if ($teacher_id === 0) {
    header('Location: ../index.php');
    exit;
}

// Load all unique sections and subjects the teacher is assigned
$loads = $teacherdb->getTeacherLoads($teacher_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Grading Sheets</title>
    <link rel="stylesheet" href="teacher_styles.css"> 
    <style>
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .classes-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.95em; }
        .classes-table th, .classes-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .classes-table th { background: #007bff; color: white; }
        .classes-table a { color: #44010b; text-decoration: none; font-weight: bold; }
        .classes-table a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="card">
        <h2>Grading Sheets</h2>
        <p>Select a subject load below to open the dedicated grade encoding sheet.</p>
        
        <table class="classes-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Section (Level)</th>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Assignment Type</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($loads)): ?>
                    <?php $count = 1; ?>
                    <?php foreach ($loads as $load): ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td>
                                <a href="grades.php?section_id=<?php echo htmlspecialchars($load['section_id']); ?>&subject_id=<?php echo htmlspecialchars($load['subject_id']); ?>">
                                    <?php echo htmlspecialchars($load['section_name'] . " (G" . $load['grade_level'] . $load['section_letter'] . ")"); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($load['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($load['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($load['assignment_type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No classes or subjects are currently assigned to you.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>