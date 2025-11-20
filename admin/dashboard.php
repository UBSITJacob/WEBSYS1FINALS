<?php
// admin/dashboard.php
session_start();
require_once "../includes/oop_functions.php";

// ---- Ensure admin is logged in ----
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$admin = $_SESSION['admin'];

// ---- Database connection ----
$db = new Database();
$conn = $db->getConnection();

// ---- Handle Logout ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit;
}

// ---- Dashboard Counts ----
// student_details table
$totalStudents = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM student_details");
if ($res) {
    $totalStudents = intval($res->fetch_assoc()['total'] ?? 0);
}

// teacher_details table
$totalTeachers = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM teacher_details");
if ($res) {
    $totalTeachers = intval($res->fetch_assoc()['total'] ?? 0);
}

// users table -> count admin users
$totalAdmins = 0;
$res = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE user_type = 'Admin'");
$res->execute();
$r = $res->get_result();
if ($r) $totalAdmins = intval($r->fetch_assoc()['total'] ?? 0);
$res->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Dashboard | Evelio AMS</title>
<link rel="stylesheet" href="css/admin_styles.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* (same styles as your latest dashboard — kept concise here) */
body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; }
.sidebar { background-color: #1a1a1a; color: white; width: 230px; height: 100vh; position: fixed; top: 0; left: 0; padding: 20px; transition: transform 0.3s ease; }
.sidebar.hidden { transform: translateX(-230px); }
.sidebar ul { list-style: none; padding: 0; }
.sidebar ul li { margin: 15px 0; position: relative; }
.sidebar ul li a { text-decoration: none; color: #ccc; font-weight: 600; display: block; padding: 8px 10px; border-radius: 5px; cursor:pointer; }
.sidebar ul li a:hover, .sidebar ul li a.active { background-color: #007bff; color: #fff; }
.dropdown-content { display: none; flex-direction: column; margin-left: 15px; }
.dropdown-content a { font-size: 14px; background-color: #2a2a2a; padding: 6px 10px; border-radius: 4px; }
.dropdown-content a:hover { background-color: #007bff; }
.dropdown.open .dropdown-content { display: flex; }
.profile-section { text-align: center; margin-bottom: 20px; }
.profile-section img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin: 0 auto; }
.profile-section p { margin: 10px 0 5px; font-weight: bold; font-size: 16px; color: #fff; }
.update-password-btn { background-color: #28a745; color: #fff; padding: 8px 14px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; }
.update-password-btn:hover { background-color: #218838; }
.header { margin-left: 230px; background: #e9ecef; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ccc; transition: margin-left 0.3s ease; }
.header.sidebar-hidden { margin-left: 0; }
.header .title { font-size: 20px; font-weight: 700; color: #333; }
.header .controls { display: flex; gap: 10px; align-items: center; }
.header button { background: #007bff; border: none; color: #fff; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; }
.header button:hover { background: #0056b3; }
.logout-btn { background-color: #dc3545 !important; }
.logout-btn:hover { background-color: #c82333 !important; }
.main-content { margin-left: 230px; padding: 25px; transition: margin-left 0.3s ease; min-height: calc(100vh - 60px); }
.main-content.sidebar-hidden { margin-left: 0; }
.dashboard-cards { display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap; }
.card { flex: 1; background: #fff; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 3px 10px rgba(0,0,0,0.1); min-width: 250px; }
.card h3 { color: #333; }
.card .count { font-size: 2rem; font-weight: bold; margin-top: 10px; color: #007bff; }
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="profile-section">
        <img src="<?= htmlspecialchars($admin['profile_pic'] ?? 'img/default.jpg'); ?>" alt="Profile Picture">
        <p><?= htmlspecialchars($admin['fullname'] ?? 'Administrator'); ?></p>

        <!-- Settings button loads settings.php dynamically -->
        <button class="update-password-btn" id="openSettings">Settings</button>
    </div>

    <ul>
        <li><a class="nav-link active" data-page="dashboard">Dashboard</a></li>

        <li class="dropdown">
            <a class="dropdown-toggle">Manage Students ▼</a>
            <div class="dropdown-content">
                <a class="nav-link" data-page="add_student_interface.php">Add Student</a>
                <a class="nav-link" data-page="manage_students.php">Manage Students</a>
            </div>
        </li>

        <!-- 🔑 NEW: Manage Sections link -->
        <li>
            <a class="nav-link" data-page="manage_sections.php">Manage Sections</a>
        </li>
        <!-- END NEW LINK -->

        <li class="dropdown">
            <a class="dropdown-toggle">Manage Teachers ▼</a>
            <div class="dropdown-content">
                <a class="nav-link" data-page="add_teacher_interface.php">Add Teacher</a>
                <a class="nav-link" data-page="manage_teachers.php">Manage Teachers</a>
            </div>
        </li>

        <li>
            <a class="nav-link" data-page="manage_roles.php">Manage Roles</a>
        </li>
    </ul>
</div>

<div class="header" id="header">
    <div class="title">Evelio AMS - Admin Dashboard</div>
    <div class="controls">
        <button id="toggleSidebar">☰</button>
        <button id="toggleFullscreen">⛶</button>
        <form method="POST" id="logoutForm" style="display:inline;">
            <button type="submit" name="logout" class="logout-btn">Sign Out</button>
        </form>
    </div>
</div>

<div class="main-content" id="mainContent">
    <h2>Overview</h2>
    <div class="dashboard-cards">
        <div class="card"><h3>Total Students</h3><div class="count"><?= $totalStudents ?></div></div>
        <div class="card"><h3>Total Teachers</h3><div class="count"><?= $totalTeachers ?></div></div>
        <div class="card"><h3>Total Admin Staff</h3><div class="count"><?= $totalAdmins ?></div></div>
    </div>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const header = document.getElementById('header');
const mainContent = document.getElementById('mainContent');

document.getElementById('toggleSidebar').onclick = () => {
    sidebar.classList.toggle('hidden');
    header.classList.toggle('sidebar-hidden');
    mainContent.classList.toggle('sidebar-hidden');
};

const fullscreenBtn = document.getElementById('toggleFullscreen');
fullscreenBtn.onclick = () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        fullscreenBtn.textContent = '⏹';
    } else {
        document.exitFullscreen();
        fullscreenBtn.textContent = '⛶';
    }
};

// Dropdown menus
document.querySelectorAll('.dropdown-toggle').forEach(el =>
    el.addEventListener('click', () => el.parentElement.classList.toggle('open'))
);

// ACTIVE LINK SETTER
function setActiveLink(page) {
    document.querySelectorAll('.nav-link').forEach(a => {
        a.classList.remove('active');
        if (a.dataset.page === page) {
            a.classList.add('active');

            const parentDropdown = a.closest('.dropdown');
            if (parentDropdown) parentDropdown.classList.add('open');
        }
    });
}

// PAGE LOADER
function loadPage(page) {
    if (!page || page === 'dashboard') {
        localStorage.removeItem('currentPage');
        location.reload();
        return;
    }

    fetch(page)
        .then(r => r.text())
        .then(html => {
            mainContent.innerHTML = html;
            localStorage.setItem('currentPage', page);
            setActiveLink(page);

            // execute inline scripts from loaded page
            const temp = document.createElement('div');
            temp.innerHTML = html;
            temp.querySelectorAll('script').forEach(oldScript => {
                const newScript = document.createElement('script');
                if (oldScript.src) newScript.src = oldScript.src;
                else newScript.textContent = oldScript.textContent;
                document.body.appendChild(newScript);
            });
        })
        .catch(err =>
            Swal.fire('Error', 'Unable to load page: ' + err.message, 'error')
        );
}

// NAV LINKS
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        loadPage(link.dataset.page);
    });
});

// SETTINGS BUTTON (TOP OF SIDEBAR)
document.getElementById('openSettings').addEventListener('click', e => {
    e.preventDefault();
    loadPage("settings.php");
});

// RESTORE LAST PAGE
window.addEventListener('DOMContentLoaded', () => {
    const lastPage = localStorage.getItem('currentPage');
    if (lastPage) {
        loadPage(lastPage);
        setActiveLink(lastPage);
    }
});

// CLEAR LAST PAGE WHEN LOGGING OUT
document.getElementById('logoutForm').addEventListener('submit', () => {
    localStorage.removeItem('currentPage');
});
</script>
</body>
</html>