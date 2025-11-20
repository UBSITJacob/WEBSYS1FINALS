<?php
require_once "teacher_functions.php";
$teacherdb = new TeacherDB();

// TEMP — hardcoded teacher ID (replace with session later)
$teacher_id = 1;

// get advisory section (if any)
$advisory = $teacherdb->getAdvisorySection($teacher_id);

// default academic year
$selected_academic_year = "2024-2025";

// allow changing academic year via GET
if (isset($_GET['academic_year'])) {
    $selected_academic_year = trim($_GET['academic_year']);
}

// load students if advisory exists
$advisory_students = [];
if ($advisory && isset($advisory['id'])) {
    $advisory_students = $teacherdb->getSectionStudents(
        (int)$advisory['id'],
        $selected_academic_year
    );
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Advisory Class</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial;
            overflow-x: hidden;
        }

        .topbar {
            width: 100%;
            background: #333;
            color: white;
            padding: 15px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10;
        }

        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 220px;
            height: calc(100vh - 60px);
            background: #2d2d2d;
            padding-top: 20px;
            overflow-y: auto;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 12px 20px;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li:hover {
            background: #444;
        }

        .content {
            margin-left: 240px;
            margin-top: 80px;
            padding: 20px;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }

        .card {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            box-shadow: 0px 0px 4px #ccc;
        }

        .form-row {
            margin-bottom: 10px;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 4px;
        }

        input[type="text"] {
            width: 100%;
            padding: 7px;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            padding: 8px 14px;
            background: #333;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #555;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 14px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 6px 8px;
        }

        .small-text {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content">

    <div class="card">
        <h2>Advisory Class</h2>

        <?php if ($advisory && isset($advisory['section_name'])): ?>
            <p>
                Section:
                <strong><?php echo htmlspecialchars($advisory['section_name']); ?></strong><br>
                Grade Level:
                <strong><?php echo htmlspecialchars($advisory['grade_level']); ?></strong><br>
                <?php if (isset($advisory['section_letter'])): ?>
                    Section Letter:
                    <strong><?php echo htmlspecialchars($advisory['section_letter']); ?></strong><br>
                <?php endif; ?>
            </p>

            <form method="get" action="advisory.php" style="max-width: 300px;">
                <div class="form-row">
                    <label for="academic_year">Academic Year (e.g. 2024-2025):</label>
                    <input type="text" name="academic_year" id="academic_year"
                           value="<?php echo htmlspecialchars($selected_academic_year); ?>" required>
                </div>
                <button type="submit">Load Students</button>
            </form>

        <?php else: ?>
            <p>You are not assigned as an adviser to any section.</p>
        <?php endif; ?>
    </div>

    <?php if ($advisory && isset($advisory['section_name'])): ?>
        <div class="card">
            <h3>Students in Advisory (AY <?php echo htmlspecialchars($selected_academic_year); ?>)</h3>

            <?php if (!empty($advisory_students)): ?>
                <table>
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>School ID</th>
                        <th>Full Name</th>
                    </tr>
                    <?php
                    $count = 1;
                    foreach ($advisory_students as $stu):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo htmlspecialchars($stu['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($stu['school_id']); ?></td>
                            <td><?php echo htmlspecialchars($stu['fullname']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p class="small-text">
                    These students are enrolled in your advisory section for the selected academic year.
                </p>
            <?php else: ?>
                <p>No students enrolled in this advisory section for AY <?php echo htmlspecialchars($selected_academic_year); ?>.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
