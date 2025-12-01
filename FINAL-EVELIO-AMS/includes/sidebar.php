<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['role'] ?? 'guest';
$user_email = $_SESSION['email'] ?? 'Guest';
$user_initials = strtoupper(substr($user_email, 0, 2));
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo $user_role === 'admin' ? 'admin_dashboard.php' : ($user_role === 'teacher' ? 'teacher_dashboard.php' : 'student_dashboard.php'); ?>" class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <img src="assets/images/school-logo.png" alt="School Logo" style="width: 50px; height: 50px; object-fit: contain;">
            </div>
            <div class="sidebar-logo-text">
                Evelio AMS
                <span>Academic Management</span>
            </div>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <?php if($user_role === 'admin'): ?>
        <div class="nav-section">
            <div class="nav-section-title"><span>Main</span></div>
            <a href="admin_dashboard.php" class="nav-item <?php echo $current_page === 'admin_dashboard.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </span>
                <span class="nav-item-text">Dashboard</span>
            </a>
            <a href="applicants.php" class="nav-item <?php echo $current_page === 'applicants.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                </span>
                <span class="nav-item-text">Applicants</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title"><span>Management</span></div>
            <a href="students.php" class="nav-item <?php echo in_array($current_page, ['students.php', 'students_add.php', 'student_view.php']) ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span class="nav-item-text">Students</span>
            </a>
            <a href="teachers.php" class="nav-item <?php echo $current_page === 'teachers.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span class="nav-item-text">Teachers</span>
            </a>
            <a href="sections.php" class="nav-item <?php echo $current_page === 'sections.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                </span>
                <span class="nav-item-text">Sections</span>
            </a>
            <a href="subject_loads.php" class="nav-item <?php echo $current_page === 'subject_loads.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </span>
                <span class="nav-item-text">Subject Loads</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title"><span>Enrollment</span></div>
            <a href="assign_section.php" class="nav-item <?php echo $current_page === 'assign_section.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <polyline points="16 11 18 13 22 9"></polyline>
                    </svg>
                </span>
                <span class="nav-item-text">Assign Section</span>
            </a>
            <a href="manage_enrollment.php" class="nav-item <?php echo $current_page === 'manage_enrollment.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </span>
                <span class="nav-item-text">Manage Enrollment</span>
            </a>
        </div>
        
        <?php elseif($user_role === 'teacher'): ?>
        <div class="nav-section">
            <div class="nav-section-title"><span>Main</span></div>
            <a href="teacher_dashboard.php" class="nav-item <?php echo $current_page === 'teacher_dashboard.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </span>
                <span class="nav-item-text">Dashboard</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title"><span>Class Management</span></div>
            <a href="advisory_class.php" class="nav-item <?php echo $current_page === 'advisory_class.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span class="nav-item-text">Advisory Class</span>
            </a>
            <a href="grades.php" class="nav-item <?php echo $current_page === 'grades.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </span>
                <span class="nav-item-text">Manage Grades</span>
            </a>
            <a href="attendance.php" class="nav-item <?php echo $current_page === 'attendance.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </span>
                <span class="nav-item-text">Attendance</span>
            </a>
        </div>
        
        <?php else: ?>
        <div class="nav-section">
            <div class="nav-section-title"><span>Main</span></div>
            <a href="student_dashboard.php" class="nav-item <?php echo $current_page === 'student_dashboard.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </span>
                <span class="nav-item-text">Dashboard</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title"><span>Academic</span></div>
            <a href="student_profile.php" class="nav-item <?php echo $current_page === 'student_profile.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span class="nav-item-text">My Profile</span>
            </a>
            <a href="student_grades.php" class="nav-item <?php echo $current_page === 'student_grades.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </span>
                <span class="nav-item-text">My Grades</span>
            </a>
            <a href="student_attendance.php" class="nav-item <?php echo $current_page === 'student_attendance.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </span>
                <span class="nav-item-text">Attendance</span>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="nav-section">
            <div class="nav-section-title"><span>Account</span></div>
            <a href="change_password.php" class="nav-item <?php echo $current_page === 'change_password.php' ? 'active' : ''; ?>">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </span>
                <span class="nav-item-text">Settings</span>
            </a>
            <a href="logout.php" class="nav-item" onclick="return confirmAction('Are you sure you want to logout?')">
                <span class="nav-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </span>
                <span class="nav-item-text">Logout</span>
            </a>
        </div>
    </nav>
    
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><?php echo $user_initials; ?></div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?php echo htmlspecialchars($user_email, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="sidebar-user-role"><?php echo ucfirst($user_role); ?></div>
            </div>
        </div>
    </div>
</aside>
<div class="mobile-overlay" id="mobileOverlay"></div>
