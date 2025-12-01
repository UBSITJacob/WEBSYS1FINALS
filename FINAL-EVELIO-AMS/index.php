<?php
include "session.php";

if(is_logged_in()) {
    $role = $_SESSION['role'] ?? '';
    if($role === 'admin') {
        header('Location: admin_dashboard.php');
    } elseif($role === 'teacher') {
        header('Location: teacher_dashboard.php');
    } else {
        header('Location: student_dashboard.php');
    }
    exit;
}

$error = '';
$db_available = false;
$pdo = null;

try {
    include "pdo_functions.php";
    $pdo = new pdoCRUD();
    $db_available = true;
} catch(Exception $e) {
    $db_available = false;
}

$demo_accounts = [
    'admin@evelio.edu' => ['id' => 1, 'email' => 'admin@evelio.edu', 'role' => 'admin', 'password' => 'admin123', 'first_login_required' => 0],
    'teacher@evelio.edu' => ['id' => 2, 'email' => 'teacher@evelio.edu', 'role' => 'teacher', 'password' => 'teacher123', 'first_login_required' => 0],
    'student@evelio.edu' => ['id' => 3, 'email' => 'student@evelio.edu', 'role' => 'student', 'password' => 'student123', 'first_login_required' => 0],
];

if(isset($_POST['login'])){
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if($db_available && $pdo) {
        $user = $pdo->login($email, $password);
        if($user){
            login_user($user);
            if($user['first_login_required']){
                header('Location: change_password.php');
                exit;
            }
            if($user['role'] === 'admin') {
                header('Location: admin_dashboard.php');
            } elseif($user['role'] === 'teacher') {
                header('Location: teacher_dashboard.php');
            } else {
                header('Location: student_dashboard.php');
            }
            exit;
        }
    }
    
    if(isset($demo_accounts[$email]) && $demo_accounts[$email]['password'] === $password) {
        login_user($demo_accounts[$email]);
        if($demo_accounts[$email]['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } elseif($demo_accounts[$email]['role'] === 'teacher') {
            header('Location: teacher_dashboard.php');
        } else {
            header('Location: student_dashboard.php');
        }
        exit;
    }
    
    $error = "Invalid email or password. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>Login - Evelio AMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/layout.css">
</head>
<body>
    <div class="auth-layout">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-logo">
                    <div class="auth-logo-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <div class="auth-logo-text">Evelio AMS</div>
                    <div class="auth-logo-subtext">Academic Management System</div>
                </div>
                
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to access your account</p>
                
                <?php if($error): ?>
                <div class="alert alert-danger mb-6">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div class="alert-content"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <?php endif; ?>
                
                <form method="post" data-validate>
                    <div class="form-group">
                        <label for="email" class="form-label required">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </span>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required autofocus>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label required">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-primary btn-lg btn-block mt-6">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        Sign In
                    </button>
                </form>
                
                <div class="auth-divider">
                    <span>or</span>
                </div>
                
                <a href="apply_consent.php" class="btn btn-secondary btn-lg btn-block">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                    Apply as New Student
                </a>
                
                <?php if(!$db_available): ?>
                <div class="alert alert-info mb-4 mt-4">
                    <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <div class="alert-content">
                        <div class="alert-title">Demo Mode Active</div>
                        <div class="text-sm">
                            Use these demo accounts to explore:<br>
                            <strong>Admin:</strong> admin@evelio.edu / admin123<br>
                            <strong>Teacher:</strong> teacher@evelio.edu / teacher123<br>
                            <strong>Student:</strong> student@evelio.edu / student123
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="auth-footer">
                    <p class="text-muted text-sm mt-8">Evelio Academic Management System</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/app.js"></script>
</body>
</html>
