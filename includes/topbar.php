<?php
$user_role = $_SESSION['role'] ?? 'guest';
$user_email = $_SESSION['email'] ?? 'Guest';
$user_initials = strtoupper(substr($user_email, 0, 2));
$display_name = explode('@', $user_email)[0];
?>
<header class="top-header">
    <div class="header-left">
        <button class="mobile-menu-toggle" id="mobileMenuToggle" title="Open menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <?php if(isset($breadcrumb)): ?>
        <nav class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $user_role === 'admin' ? 'admin_dashboard.php' : ($user_role === 'teacher' ? 'teacher_dashboard.php' : 'student_dashboard.php'); ?>">Home</a></li>
            <?php foreach($breadcrumb as $item): ?>
            <?php if(isset($item['active']) && $item['active']): ?>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></li>
            <?php else: ?>
            <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>
    </div>
    
    <div class="header-right">
        <div class="dropdown">
            <button class="header-profile dropdown-toggle">
                <div class="header-profile-avatar"><?php echo $user_initials; ?></div>
                <div class="header-profile-info">
                    <div class="header-profile-name"><?php echo htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="header-profile-role"><?php echo ucfirst($user_role); ?></div>
                </div>
                <svg class="header-profile-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <div class="dropdown-menu">
                <a href="change_password.php" class="dropdown-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    Settings
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item danger" onclick="return confirmAction('Are you sure you want to logout?')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Logout
                </a>
            </div>
        </div>
    </div>
</header>
