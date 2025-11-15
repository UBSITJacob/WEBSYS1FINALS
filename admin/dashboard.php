<?php
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

// ---- Handle Update Account Form ----
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_account"])) {
    $newUsername = trim($_POST["new_username"]);
    $newFullname = trim($_POST["new_fullname"]);
    $oldPassword = trim($_POST["old_password"]);
    $newPassword = trim($_POST["new_password"]);
    $confirmPassword = trim($_POST["confirm_password"]);
    $id = $admin['id'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $current = $stmt->get_result()->fetch_assoc();

    if (!$current) {
        $error = "Account not found.";
    } else {
        if ($current['password'] !== $oldPassword) {
            $error = "Incorrect current password.";
        } else {
            $updates = [];
            $params = [];
            $types = "";

            if (!empty($newUsername) && $newUsername !== $current['username']) {
                $updates[] = "username = ?";
                $params[] = $newUsername;
                $types .= "s";
            }

            if (!empty($newFullname) && $newFullname !== $current['fullname']) {
                $updates[] = "fullname = ?";
                $params[] = $newFullname;
                $types .= "s";
            }

            if (!empty($newPassword)) {
                if (strlen($newPassword) < 8) {
                    $error = "Password must be at least 8 characters long.";
                } elseif ($newPassword !== $confirmPassword) {
                    $error = "New passwords do not match.";
                } else {
                    $updates[] = "password = ?";
                    $params[] = $newPassword;
                    $types .= "s";
                }
            }

            if (empty($error) && !empty($updates)) {
                $sql = "UPDATE admin SET " . implode(", ", $updates) . " WHERE id = ?";
                $params[] = $id;
                $types .= "i";
                $update = $conn->prepare($sql);
                $update->bind_param($types, ...$params);

                if ($update->execute()) {
                    $_SESSION['admin']['username'] = $newUsername ?: $current['username'];
                    $_SESSION['admin']['fullname'] = $newFullname ?: $current['fullname'];
                    $_SESSION['admin']['password'] = $newPassword ?: $current['password'];
                    $success = "Account updated successfully!";
                } else {
                    $error = "Update failed. Try again.";
                }
            } elseif (empty($error) && empty($updates)) {
                $error = "No changes detected.";
            }
        }
    }
}

// ---- Dashboard Counts ----
$totalStudents = $conn->query("SELECT COUNT(*) AS total FROM student_details")->fetch_assoc()['total'] ?? 0;
$totalTeachers = $conn->query("SELECT COUNT(*) AS total FROM teacher_details")->fetch_assoc()['total'] ?? 0;
$totalAdmins   = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Dashboard | Evelio AMS</title>
<link rel="stylesheet" href="css/admin_styles.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
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
.main-content { margin-left: 230px; padding: 25px; transition: margin-left 0.3s ease; }
.main-content.sidebar-hidden { margin-left: 0; }
.dashboard-cards { display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap; }
.card { flex: 1; background: #fff; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 3px 10px rgba(0,0,0,0.1); min-width: 250px; }
.card h3 { color: #333; }
.card .count { font-size: 2rem; font-weight: bold; margin-top: 10px; color: #007bff; }
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
.modal-content { background-color: white; margin: 8% auto; padding: 20px; width: 350px; border-radius: 10px; text-align: center; position: relative; }
.modal-content h3 { margin-bottom: 15px; }
.modal-content label { display: block; text-align: left; font-weight: 600; margin: 5px 0 3px 20px; color: #333; }
.modal-content input { width: 90%; padding: 8px; margin: 3px 0 10px; border: 1px solid #ccc; border-radius: 5px; }
.modal-content button { background-color: #007bff; color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; }
.modal-content button:hover { background-color: #0056b3; }
.close-btn { color: #aaa; position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; }
.close-btn:hover { color: black; }
</style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="profile-section">
        <img src="<?= htmlspecialchars($admin['profile_pic'] ?? 'img/default.jpg'); ?>" alt="Profile Picture">
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

        <!-- ✅ NEW MANAGE TEACHERS DROPDOWN -->
        <li class="dropdown">
            <a class="dropdown-toggle">Manage Teachers ▼</a>
            <div class="dropdown-content">
                <a class="nav-link" data-page="add_teacher_interface.php">Add Teacher</a>
                <a class="nav-link" data-page="manage_teachers.php">Manage Teachers</a>
            </div>
        </li>

        <li><a class="nav-link" data-page="manage_roles.php">Manage Roles</a></li>
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

<!-- Settings Modal -->
<div id="settingsModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h3>Account Settings</h3>
        <form id="settingsForm">
            <label for="new_fullname">Full Name</label>
            <input type="text" id="new_fullname" name="new_fullname" value="<?= htmlspecialchars($admin['fullname']); ?>">
            <label for="new_username">Username</label>
            <input type="text" id="new_username" name="new_username" value="<?= htmlspecialchars($admin['username']); ?>">
            <input type="password" name="old_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password (min 8 characters)">
            <input type="password" name="confirm_password" placeholder="Confirm New Password">
            <button type="submit" name="update_account">Save Changes</button>
        </form>
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

// --- Dropdown menus ---
document.querySelectorAll('.dropdown-toggle').forEach(el =>
    el.addEventListener('click', () => el.parentElement.classList.toggle('open'))
);

// ✅ Dynamic page loading + persistence + highlight active link
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

            const temp = document.createElement('div');
            temp.innerHTML = html;
            temp.querySelectorAll('script').forEach(oldScript => {
                const newScript = document.createElement('script');
                if (oldScript.src) newScript.src = oldScript.src;
                else newScript.textContent = oldScript.textContent;
                document.body.appendChild(newScript);
            });
        })
        .catch(err => Swal.fire('Error', 'Unable to load page: ' + err.message, 'error'));
}

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        const page = link.dataset.page;
        loadPage(page);
    });
});

window.addEventListener('DOMContentLoaded', () => {
    const lastPage = localStorage.getItem('currentPage');
    if (lastPage) {
        loadPage(lastPage);
        setActiveLink(lastPage);
    }
});

// ✅ Account settings modal
const settingsForm = document.getElementById('settingsForm');
const modal = document.getElementById('settingsModal');

if (settingsForm) {
    let originalData = {};
    document.getElementById('openSettings').onclick = () => {
        modal.style.display = 'block';
        originalData = {
            fullname: document.getElementById('new_fullname').value.trim(),
            username: document.getElementById('new_username').value.trim()
        };
    };

    document.getElementById('closeModal').onclick = () => modal.style.display = 'none';
    window.onclick = e => { if (e.target == modal) modal.style.display = 'none'; };

    settingsForm.addEventListener('submit', e => {
        e.preventDefault();
        const newFullname = document.getElementById('new_fullname').value.trim();
        const newUsername = document.getElementById('new_username').value.trim();
        const oldPass = settingsForm.querySelector('[name="old_password"]').value.trim();
        const newPass = settingsForm.querySelector('[name="new_password"]').value.trim();
        const confirmPass = settingsForm.querySelector('[name="confirm_password"]').value.trim();

        if (newPass && newPass.length < 8)
            return Swal.fire('Error', 'New password must be at least 8 characters long.', 'error');

        const hasChanges = (newFullname !== originalData.fullname) ||
                           (newUsername !== originalData.username) ||
                           (newPass.length > 0);

        if (!hasChanges) return Swal.fire('Info', 'No changes detected.', 'info');
        if (newPass && newPass !== confirmPass)
            return Swal.fire('Error', 'New passwords do not match.', 'error');
        if (newPass && !oldPass)
            return Swal.fire('Error', 'Please enter your current password.', 'error');

        const formData = new FormData(settingsForm);
        formData.append('update_account', '1');

        fetch('', { method: 'POST', body: formData })
            .then(r => r.text())
            .then(html => {
                if (html.includes('Account updated successfully!')) {
                    Swal.fire('Success', 'Account updated successfully!', 'success')
                        .then(() => location.reload());
                } else if (html.includes('Incorrect current password')) {
                    Swal.fire('Error', 'Incorrect current password.', 'error');
                } else if (html.includes('Password must be at least 8 characters long')) {
                    Swal.fire('Error', 'Password must be at least 8 characters long.', 'error');
                } else if (html.includes('New passwords do not match')) {
                    Swal.fire('Error', 'New passwords do not match.', 'error');
                } else if (html.includes('No changes detected')) {
                    Swal.fire('Info', 'No changes detected.', 'info');
                } else {
                    Swal.fire('Error', 'Update failed. Try again.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Unable to update account.', 'error'));
    });
}

// ✅ Clear saved page when signing out
document.getElementById('logoutForm').addEventListener('submit', () => {
    localStorage.removeItem('currentPage');
});
</script>
</body>
</html>
