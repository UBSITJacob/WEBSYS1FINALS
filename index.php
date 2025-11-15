<?php
session_start();
require_once "includes/oop_functions.php";

$login = new Login();
$message = "";
$showForm = ""; // Track which form to show after error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login_admin'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $admin = $login->loginAdmin($username, $password);
        if ($admin) {
            $_SESSION['admin'] = $admin;
            header("Location: admin/dashboard.php");
            exit;
        } else {
            $message = "Invalid Admin Credentials!";
            $showForm = "admin";
        }
    }

    if (isset($_POST['login_teacher'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $teacher = $login->loginTeacher($username, $password);
        if ($teacher) {
            $_SESSION['teacher'] = $teacher;
            header("Location: teacher/dashboard.php");
            exit;
        } else {
            $message = "Invalid Teacher Credentials!";
            $showForm = "teacher";
        }
    }

    if (isset($_POST['login_student'])) {
        $schoolID = $_POST['school_id'];
        $birthdate = $_POST['birthdate'];
        $password = $_POST['password'];
        $student = $login->loginStudent($schoolID, $birthdate, $password);
        if ($student) {
            $_SESSION['student'] = $student;
            header("Location: student/dashboard.php");
            exit;
        } else {
            $message = "Invalid Student Credentials!";
            $showForm = "student";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evelio AMS Portal | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <style>
    * { box-sizing: border-box; font-family: "Segoe UI", Arial, sans-serif; }

    body {
        background-color: #f0f4f8;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .login-box {
        background-color: #fff;
        padding: 35px 40px;
        border-radius: 12px;
        width: 380px;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .login-box img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        margin-bottom: 15px;
    }

    .login-box h2 {
        font-size: 1.3rem;
        color: #0d47a1;
        margin-bottom: 25px;
    }

    /* Selection buttons */
    .btn-option {
        width: 100%;
        margin: 6px 0;
        padding: 10px;
        border: none;
        background-color: #1976d2;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.95rem;
        transition: background-color 0.2s, transform 0.1s;
    }

    .btn-option:hover { background-color: #1565c0; transform: scale(1.02); }

    .btn-register {
        background-color: #2e7d32;
        color: white;
    }

    .btn-register:hover { background-color: #1b5e20; }

    form {
        display: none;
        margin-top: 15px;
    }

    input[type="text"],
    input[type="password"],
    input[type="date"] {
        width: 100%;
        padding: 8px;
        margin: 6px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 0.95rem;
    }

    input[type="submit"] {
        width: 100%;
        padding: 10px;
        background-color: #1565c0;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.2s, transform 0.1s;
    }

    input[type="submit"]:hover { background-color: #0d47a1; transform: scale(1.02); }

    .back-btn {
        background-color: #9e9e9e;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        margin-top: 10px;
        cursor: pointer;
        width: 100%;
        font-weight: 600;
    }

    .back-btn:hover { background-color: #757575; }

    .message {
        background: #ffebee;
        color: #b71c1c;
        padding: 8px;
        border: 1px solid #ef9a9a;
        border-radius: 4px;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    /* Modal styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 25px;
        width: 90%;
        max-width: 500px;
        border-radius: 10px;
        text-align: justify;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
    }

    .modal h3 {
        text-align: center;
        color: #0d47a1;
    }

    .modal button {
        padding: 8px 15px;
        margin: 8px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
    }

    .agree {
        background-color: #2e7d32;
        color: #fff;
    }

    .agree:hover { background-color: #1b5e20; }

    .disagree {
        background-color: #c62828;
        color: #fff;
    }

    .disagree:hover { background-color: #b71c1c; }
</style>

</head>
<body>

<div class="login-box">
    <img src="assets/images/logo.jpg" alt="School Logo">
    <h2>Academic Management System</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- User Selection -->
    <div id="user-selection">
        <label>Select User:</label>
        <button class="btn-option" onclick="showForm('admin')">Admin</button>
        <button class="btn-option" onclick="showForm('teacher')">Teacher</button>
        <button class="btn-option" onclick="showForm('student')">Student</button>
        <button class="btn-option btn-register" onclick="openConsent()">Apply as New Student / Register</button>
    </div>

    <!-- Admin Login -->
    <form method="POST" id="admin">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login_admin" value="Login as Admin">
        <button type="button" class="back-btn" onclick="goBack()">Back</button>
    </form>

    <!-- Teacher Login -->
    <form method="POST" id="teacher">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login_teacher" value="Login as Teacher">
        <button type="button" class="back-btn" onclick="goBack()">Back</button>
    </form>

    <!-- Student Login -->
    <form method="POST" id="student">
        <input type="text" name="school_id" placeholder="School ID" required>
        <input type="text" name="birthdate" placeholder="Birthdate (MM/DD/YYYY)" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login_student" value="Login as Student">
        <button type="button" class="back-btn" onclick="goBack()">Back</button>
    </form>
</div>

<!-- Consent Modal -->
<div class="modal" id="consentModal">
    <div class="modal-content">
        <h3>CONSENT AGREEMENT</h3>
        <p>
            I have read and understood the Evelio Javier Memorial National High School Privacy Policy. 
            I hereby give my consent for the processing of my personal data by authorized personnel 
            for academic management purposes.
        </p>
        <div style="text-align:center;">
            <button class="agree" onclick="agreeConsent()">Agree</button>
            <button class="disagree" onclick="closeConsent()">Disagree</button>
        </div>
    </div>
</div>

<script>
function showForm(type) {
    document.getElementById('user-selection').style.display = 'none';
    document.querySelectorAll('form').forEach(f => f.style.display = 'none');
    const selectedForm = document.getElementById(type);
    if (selectedForm) selectedForm.style.display = 'block';
}

function goBack() {
    document.querySelectorAll('form').forEach(f => f.style.display = 'none');
    document.getElementById('user-selection').style.display = 'block';
}

function openConsent() {
    document.getElementById('consentModal').style.display = 'flex';
}

function closeConsent() {
    document.getElementById('consentModal').style.display = 'none';
}

function agreeConsent() {
    window.location.href = "register.php";
}

// Auto-show form on error
<?php if (!empty($showForm)): ?>
    document.addEventListener("DOMContentLoaded", () => {
        showForm("<?= $showForm ?>");
    });
<?php endif; ?>
</script>

</body>
</html>
