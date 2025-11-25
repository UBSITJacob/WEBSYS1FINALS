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
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

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
    </style>
</head>
<body>

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
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
    <div id="message" class="mt-3"></div>
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
