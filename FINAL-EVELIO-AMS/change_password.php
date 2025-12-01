<?php
include "session.php";
require_login();

$page_title = 'Change Password';
$db_available = false;
$pdoC = null;

try {
    include "pdo_functions.php";
    $pdoC = new pdoCRUD();
    $db_available = true;
} catch(Exception $e) {
    $db_available = false;
}

$success = false;
$error = '';
$demo_mode = !$db_available;

if(isset($_POST['change'])){
    if($demo_mode) {
        $error = "Demo Mode: Password changes are disabled. Connect a database to enable this feature.";
    } else {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if(strlen($new) < 8){
            $error = "New password must be at least 8 characters long.";
        } elseif($new !== $confirm){
            $error = "New passwords do not match.";
        } else {
            try {
                $ok = $pdoC->changePassword($_SESSION['account_id'],$current,$new);
                if($ok){
                    $success = true;
                } else {
                    $error = "Invalid current password. Please try again.";
                }
            } catch(Exception $e) {
                $error = "Database error. Please try again later.";
            }
        }
    }
}

$user_role = $_SESSION['role'] ?? 'guest';
$dashboard_url = $user_role === 'admin' ? 'admin_dashboard.php' : ($user_role === 'teacher' ? 'teacher_dashboard.php' : 'student_dashboard.php');

$breadcrumb = [
    ['title' => 'Settings', 'active' => true]
];
?>
<?php include "includes/header.php"; ?>
<div class="app-layout">
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <main class="main-content">
            <div class="page-header">
                <div class="page-header-row">
                    <div>
                        <h1 class="page-header-title">Account Settings</h1>
                        <p class="page-header-subtitle">Manage your account security and preferences.</p>
                    </div>
                </div>
            </div>
            
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--spacing-2); vertical-align: middle;">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            Change Password
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if($success): ?>
                        <div class="alert alert-success" style="margin-bottom: var(--spacing-5);">
                            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <div class="alert-content">
                                <div class="alert-title">Password Changed Successfully</div>
                                <div>Your password has been updated. You can continue using the system with your new password.</div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger" style="margin-bottom: var(--spacing-5);">
                            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                            <div class="alert-content">
                                <div class="alert-title">Error</div>
                                <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <div class="form-group">
                                <label class="form-label required">Current Password</label>
                                <input type="password" name="current_password" class="form-control" placeholder="Enter your current password" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">New Password</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Enter new password" minlength="8" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" minlength="8" required>
                            </div>
                            
                            <div class="d-flex gap-3" style="margin-top: var(--spacing-6);">
                                <button type="submit" name="change" class="btn btn-primary">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    Update Password
                                </button>
                                <a href="<?php echo $dashboard_url; ?>" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--spacing-2); vertical-align: middle;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Password Requirements
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted" style="margin-bottom: var(--spacing-4);">Your new password must meet the following requirements:</p>
                        
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="display: flex; align-items: center; gap: var(--spacing-3); padding: var(--spacing-3) 0; border-bottom: 1px solid var(--color-border-light);">
                                <span class="stat-card-icon success" style="width: 32px; height: 32px; margin: 0;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span>At least 8 characters long</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: var(--spacing-3); padding: var(--spacing-3) 0; border-bottom: 1px solid var(--color-border-light);">
                                <span class="stat-card-icon accent" style="width: 32px; height: 32px; margin: 0;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                </span>
                                <span>Should be different from current password</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: var(--spacing-3); padding: var(--spacing-3) 0; border-bottom: 1px solid var(--color-border-light);">
                                <span class="stat-card-icon primary" style="width: 32px; height: 32px; margin: 0;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <span>Avoid using easily guessable passwords</span>
                            </li>
                            <li style="display: flex; align-items: center; gap: var(--spacing-3); padding: var(--spacing-3) 0;">
                                <span class="stat-card-icon warning" style="width: 32px; height: 32px; margin: 0;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                        <line x1="12" y1="9" x2="12" y2="13"></line>
                                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                    </svg>
                                </span>
                                <span>Never share your password with anyone</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include "includes/footer.php"; ?>
