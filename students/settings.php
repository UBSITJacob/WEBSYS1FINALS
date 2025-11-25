<?php
session_start();
require_once "oop_functions.php";

// Check if student is logged in
if (!isset($_SESSION['student'])) {
    header("Location: ../index.php");
    exit;
}

$student_id = $_SESSION['student']['id'];
$student = new Student($student_id);
$profile = $student->getProfile();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: #1a1a1a;
            color: white;
            width: 230px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-230px); /* Hide the sidebar */
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #ccc;
            font-weight: 600;
            display: block;
            padding: 8px 10px;
            border-radius: 5px;
        }

        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: #007bff;
            color: #fff;
        }

        /* Header Styles */
        .header {
            margin-left: 230px;
            background: #e9ecef;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ccc;
            transition: 0.3s;
        }

        .header.sidebar-hidden {
            margin-left: 0;
        }

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

        .header button:hover {
            background: #0056b3;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        /* Main content */
        .main-content {
            margin-left: 230px;
            padding: 90px 25px 25px;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-hidden {
            margin-left: 0;
        }

        /* Form styles */
        .container {
            max-width: 500px;
            margin-top: 50px;
        }

        .form-label {
            font-weight: 600;
        }

        .btn-primary {
            font-weight: 700;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        /* Error/Success message styles */
        #message {
            margin-top: 20px;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-success {
            color: #28a745;
        }

        /* Profile section styles */
        .profile-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-section img {
            width: 90px; /* Set image size to 90px */
            height: 90px; /* Ensure square aspect */
            border-radius: 50%; /* Make image circular */
            object-fit: cover; /* Ensure image fits the circle */
            display: block;
            margin: auto;
        }

        .profile-section p {
            margin-top: 10px;
            font-size: 1.2rem;
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="profile-section">
        <img src="https://cdn-icons-png.flaticon.com/512/3870/3870822.png" alt="Profile pic">
        <p><?= htmlspecialchars($profile['fullname']) ?></p>
        <!-- Settings button -->
        <button class="update-password-btn" id="openSettings">Settings</button>
    </div>
    <ul>
        <li><a href="index.php" class="active">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="grades.php">Grades</a></li>
        <li><a href="enrollment.php">Enrollment</a></li>
    </ul>
</div>

<!-- HEADER -->
<div class="header" id="header">
    <div class="title">Change Password</div>
    <div>
        <button id="toggleSidebar" class="btn btn-primary">☰</button>
        <a href="logout.php" class="btn logout-btn">Logout</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">
    <div class="container">
        <h2>Change Password</h2>
        <form id="changePasswordForm">
            <div class="mb-3">
                <label for="old_password" class="form-label">Old Password</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-success">Save Changes</button>
        </form>
        <div id="message" class="mt-3"></div>
    </div>
</div>

<script>
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get the form values
        const oldPassword = document.getElementById('old_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Validate the new passwords match
        if (newPassword !== confirmPassword) {
            document.getElementById('message').innerText = 'Passwords do not match.';
            document.getElementById('message').classList.add('text-danger');
            return;
        }

        // Send the data to the server
        const formData = new FormData();
        formData.append('old_password', oldPassword);
        formData.append('new_password', newPassword);

        fetch('update_password.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('message').innerText = data.message;
                document.getElementById('message').classList.add('text-success');
            } else {
                document.getElementById('message').innerText = data.message;
                document.getElementById('message').classList.add('text-danger');
            }
        })
        .catch(error => {
            document.getElementById('message').innerText = 'An error occurred.';
            document.getElementById('message').classList.add('text-danger');
        });
    });
</script>

</body>
</html>
