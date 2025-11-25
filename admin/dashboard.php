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
$totalStudents = $conn->query("SELECT COUNT(*) AS total FROM student_details")->fetch_assoc()['total'] ?? 0;
$totalTeachers = $conn->query("SELECT COUNT(*) AS total FROM teacher_details")->fetch_assoc()['total'] ?? 0;

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE user_type = 'Admin'");
$stmt->execute();
$totalAdmins = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Dashboard | Evelio AMS</title>
<link rel="stylesheet" href="css/admin_styles.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* GLOBAL */
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #f4f4f4;
}

/* SIDEBAR */
.sidebar {
    background: #1a1a1a;
    color: white;
    width: 230px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    padding: 20px;
    overflow-y: scroll;
    overflow-x: hidden;
    scroll-behavior: smooth;
    transition: transform 0.3s ease;
}

.sidebar.hidden { 
    transform: translateX(-230px); 
}

.sidebar::-webkit-scrollbar { width: 8px; }
.sidebar::-webkit-scrollbar-track { background: #1a1a1a; }
.sidebar::-webkit-scrollbar-thumb {
    background: #444; 
    border-radius: 3px;
}
.sidebar::-webkit-scrollbar-thumb:hover { background: #666; }

.profile-section {
    text-align: center;
    margin-bottom: 20px;
}
.profile-section img {
    width: 100px; height: 100px;
    border-radius: 50%;
    object-fit: cover;
    display: block; margin: auto;
}
.profile-section p {
    margin: 10px 0 5px;
    font-weight: bold;
    color: #fff;
}
.update-password-btn {
    background: #28a745;
    color: #fff;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.sidebar ul { list-style: none; padding: 0; }
.sidebar ul li { margin: 15px 0; }
.sidebar ul li a {
    color: #ccc;
    text-decoration: none;
    display: block;
    padding: 8px 10px;
    border-radius: 5px;
    font-weight: 600;
}
.sidebar ul li a:hover,
.sidebar ul li a.active {
    background: #007bff;
    color: #fff;
}

/* DROPDOWN */
.dropdown-content {
    display: none;
    flex-direction: column;
    margin-left: 15px;
}
.dropdown.open .dropdown-content { display: flex; }
.dropdown-content a {
    background: #2a2a2a;
    padding: 6px;
    border-radius: 4px;
    font-size: 14px;
}

/* HEADER */
.header {
    position: fixed;
    top: 0;
    left: 230px;
    right: 0;
    height: 60px;
    background: #e9ecef;
    border-bottom: 1px solid #ccc;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    z-index: 999;
    transition: left 0.3s ease;
}
.header.sidebar-hidden { left: 0; }

.header .title {
    font-size: 20px;
    font-weight: bold;
    color: #333;
}

.controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.header button {
    background: #007bff;
    border: none;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
}
.header button:hover { background: #0056b3; }
.logout-btn { background: #dc3545 !important; }
.logout-btn:hover { background: #c82333 !important; }

/* MAIN */
.main-content {
    margin-left: 230px;
    padding: 90px 25px 25px;
    transition: margin-left 0.3s ease;
}
.main-content.sidebar-hidden { margin-left: 0 !important; }

.dashboard-cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}
.card {
    background: #fff;
    padding: 20px;
    min-width: 250px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.card .count {
    font-size: 2rem;
    color: #007bff;
    font-weight: bold;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="profile-section">
        <img src="<?= htmlspecialchars($admin['profile_pic'] ?? 'img/default.jpg'); ?>">
        <p><?= htmlspecialchars($admin['fullname'] ?? 'Administrator'); ?></p>
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

        <!-- UPDATED: Manage Sections dropdown -->
        <li class="dropdown">
            <a class="dropdown-toggle">Manage Sections ▼</a>
            <div class="dropdown-content">
                <a class="nav-link" data-page="All_Sections/grade7.php">Grade 7</a>
                <a class="nav-link" data-page="All_Sections/grade8.php">Grade 8</a>
                <a class="nav-link" data-page="All_Sections/grade9.php">Grade 9</a>
                <a class="nav-link" data-page="All_Sections/grade10.php">Grade 10</a>
                <a class="nav-link" data-page="All_Sections/grade11.php">Grade 11</a>
                <a class="nav-link" data-page="All_Sections/grade12.php">Grade 12</a>
            </div>
        </li>

        <li class="dropdown">
            <a class="dropdown-toggle">Manage Teachers ▼</a>
            <div class="dropdown-content">
                <a class="nav-link" data-page="add_teacher_interface.php">Add Teacher</a>
                <a class="nav-link" data-page="manage_teachers.php">Manage Teachers</a>
            </div>
        </li>

        <li class="dropdown">
            <a class="dropdown-toggle">Manage Roles ▼</a>
            <div class="dropdown-content">
                <a class="nav-link" data-page="add_admin_interface.php">Add Admin</a>
                <a class="nav-link" data-page="manage_admins.php">Manage Admin</a>
            </div>
        </li>

    </ul>

</div>

<!-- HEADER -->
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

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">
    <h2>Overview</h2>

    <div class="dashboard-cards">
        <div class="card"><h3>Total Students</h3><div class="count"><?= $totalStudents ?></div></div>
        <div class="card"><h3>Total Teachers</h3><div class="count"><?= $totalTeachers ?></div></div>
        <div class="card"><h3>Total Admin Staff</h3><div class="count"><?= $totalAdmins ?></div></div>
    </div>
</div>

<script>
// Sidebar toggle
document.getElementById("toggleSidebar").onclick = () => {
    document.getElementById("sidebar").classList.toggle("hidden");
    document.getElementById("header").classList.toggle("sidebar-hidden");
    document.getElementById("mainContent").classList.toggle("sidebar-hidden");
};

// Fullscreen toggle
document.getElementById("toggleFullscreen").onclick = () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        toggleFullscreen.textContent = "⏹";
    } else {
        document.exitFullscreen();
        toggleFullscreen.textContent = "⛶";
    }
};

// Dropdown toggles
document.querySelectorAll('.dropdown-toggle').forEach(btn => {
    btn.addEventListener("click", () =>
        btn.parentElement.classList.toggle("open")
    );
});

// Load pages dynamically
function loadPage(page) {
    if (!page || page === "dashboard") {
        localStorage.removeItem("currentPage");
        location.reload();
        return;
    }

    fetch(page)
        .then(r => r.text())
        .then(html => {
            mainContent.innerHTML = html;
            localStorage.setItem("currentPage", page);
            setActiveLink(page);

            // Execute inline scripts
            const temp = document.createElement("div");
            temp.innerHTML = html;
            temp.querySelectorAll("script").forEach(oldScript => {
                const newScript = document.createElement("script");
                if (oldScript.src) newScript.src = oldScript.src;
                else newScript.textContent = oldScript.textContent;
                document.body.appendChild(newScript);
            });
        });
}

// Active nav highlight
function setActiveLink(page) {
    document.querySelectorAll(".nav-link").forEach(a => {
        a.classList.remove("active");
        if (a.dataset.page === page) {
            a.classList.add("active");

            const dropdown = a.closest(".dropdown");
            if (dropdown) dropdown.classList.add("open");
        }
    });
}

// Click events
document.querySelectorAll(".nav-link").forEach(link => {
    link.addEventListener("click", e => {
        e.preventDefault();
        loadPage(link.dataset.page);
    });
});

// Settings
document.getElementById("openSettings").onclick = () =>
    loadPage("settings.php");

// Restore last page
window.addEventListener("DOMContentLoaded", () => {
    const last = localStorage.getItem("currentPage");
    if (last) {
        loadPage(last);
        setActiveLink(last);
    }
});

// Clear last page on logout
document.getElementById("logoutForm").onsubmit = () =>
    localStorage.removeItem("currentPage");
</script>

</body>
</html>
