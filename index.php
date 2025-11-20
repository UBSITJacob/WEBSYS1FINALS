<?php
session_start();
require_once "includes/oop_functions.php";

$login = new Login();
$message = "";

// HANDLE LOGIN REQUEST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Try login using universal function
    $user = $login->loginUser($email, $password);

    if ($user) {
        // Save correct session
        if ($user['user_type'] == "Admin") {
            $_SESSION['admin'] = $user;
            header("Location: admin/dashboard.php");
            exit;
        }
        if ($user['user_type'] == "Teacher") {
            $_SESSION['teacher'] = $user;
            header("Location: teacher/index.php");
            exit;
        }
        if ($user['user_type'] == "Student") {
            $_SESSION['student'] = $user;
            header("Location: student/dashboard.php");
            exit;
        }
    }

    // Login failed
    $message = "Invalid Email or Password!";
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

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

input[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #1565c0;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #0d47a1;
}

.message {
    background: #ffebee;
    color: #b71c1c;
    padding: 8px;
    border-radius: 5px;
    margin-bottom: 12px;
    border: 1px solid #ef9a9a;
}

.btn-register {
    margin-top: 10px;
    background-color: #2e7d32;
    color: #fff;
    padding: 10px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    width: 100%;
    font-weight: 600;
}
.btn-register:hover {
    background-color: #1b5e20;
}

/* MODAL */
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
    max-width: 550px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
}

.modal h3 {
    text-align: center;
    color: #0d47a1;
    font-size: 1.4rem;
}

.modal p {
    text-align: justify;
    font-size: 1rem;
    margin: 20px 0;
}

.modal button {
    padding: 10px 30px;
    margin: 10px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1rem;
}

.agree { background-color: #2e7d32; color: #fff; }
.agree:hover { background-color: #1b5e20; }

.disagree { background-color: #c62828; color: #fff; }
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

    <!-- UNIFIED LOGIN FORM -->
    <form method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>

    <!-- APPLY AS NEW STUDENT -->
    <button class="btn-register" onclick="openConsent()">Apply as New Student / Register</button>
</div>

<!-- CONSENT MODAL -->
<div class="modal" id="consentModal">
    <div class="modal-content">
        <h3>CONSENT AGREEMENT</h3>

        <p>
            I have read and understood the Evelio Javier Memorial National High School Privacy Policy.
            I hereby give my consent for the processing of my personal data by authorized personnel 
            for academic management purposes.
        </p>

        <button class="agree" onclick="agreeConsent()">Agree</button>
        <button class="disagree" onclick="closeConsent()">Disagree</button>
    </div>
</div>

<script>
function openConsent() {
    document.getElementById('consentModal').style.display = 'flex';
}

function closeConsent() {
    document.getElementById('consentModal').style.display = 'none';
}

function agreeConsent() {
    window.location.href = "register.php";
}
</script>

</body>
</html>
